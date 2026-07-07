<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Services\PrintService;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    protected SaleService $saleService;

    public function __construct(SaleService $saleService)
    {
        $this->saleService = $saleService;
    }

    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal']);

        if (!auth()->user()->can('ventas.view') && auth()->user()->can('ventas.view.own')) {
            $query->where('user_id', auth()->id());
        }

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $ventas = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $data = $this->saleService->getCreationData();

        if ($data['sesion'] === null) {
            return redirect()->route('cajas.index')
                ->with('error', 'Necesitas abrir una caja antes de vender.');
        }

        return view('ventas.create', $data);
    }

    public function store(StoreVentaRequest $request)
    {
        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Tu caja se cerró. No se puede registrar la venta.'], 400);
            }
            return back()->with('error', 'Tu caja se cerró. No se puede registrar la venta.');
        }

        try {
            $venta = $this->saleService->createSale($request->validated(), $sesion);
            $msg = 'Venta registrada en ' . $sesion->caja->nombre;

            if ($request->wantsJson()) {
                return response()->json([
                    'success'           => true,
                    'venta_id'          => $venta->id,
                    'total'             => (float) $venta->total,
                    'cliente'           => $venta->cliente->nombre ?? 'Consumidor Final',
                    'metodo_pago'       => $request->input('metodo_pago', 'efectivo'),
                    'tipo_comprobante'  => $request->input('tipo_comprobante', 'sin'),
                ]);
            }

            return redirect()->route('ventas.show', $venta->id)->with('success', $msg);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $venta = Venta::with([
            'cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal',
            'detalles.producto', 'detalles.almacen'
        ])->findOrFail($id);

        return view('ventas.show', compact('venta'));
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->hasAnyRole(['admin', 'admin-business', 'root']) && $user->role !== 'admin') {
            abort(403, 'Solo los administradores pueden anular ventas.');
        }

        $request->validate([
            'motivo'   => 'required|string|min:5|max:500',
            'confirmar' => 'required|accepted',
        ]);

        try {
            $this->saleService->cancelSale($id, trim($request->motivo));

            return redirect()->route('ventas.index')
                ->with('success', 'Venta #' . str_pad($id, 5, '0', STR_PAD_LEFT) . ' anulada.');
        } catch (\Exception $e) {
            return back()->withErrors('Error al anular: ' . $e->getMessage());
        }
    }

    public function buscarProducto(Request $request)
    {
        $termino = trim((string) $request->input('q', ''));
        if (strlen($termino) < 2) {
            return response()->json([]);
        }

        $productos = Producto::where(function ($q) use ($termino) {
            $q->where('nombre', 'like', '%' . $termino . '%')
              ->orWhere('codigo_barras', $termino)
              ->orWhere('codigo_barras', 'like', '%' . $termino . '%');
        })->orderBy('nombre')->limit(20)
            ->get(['id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'unidad_medida', 'imagen']);

        return response()->json($productos);
    }

    public function buscarPorCodigoBarras($codigo)
    {
        $producto = Producto::where('codigo_barras', $codigo)->first();
        if (!$producto) {
            return response()->json(['encontrado' => false], 404);
        }
        return response()->json(['encontrado' => true, 'producto' => $producto]);
    }

    public function cambiarCaja(Request $request)
    {
        $data = $request->validate(['caja_id' => 'required|exists:cajas,id']);
        $sesion = Caja::findOrFail($data['caja_id'])->sesionActiva();

        if (!$sesion || $sesion->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes una sesión abierta en esa caja.');
        }

        return redirect()->route('ventas.create');
    }

    public function exportPdf($id)
    {
        $venta = Venta::with(['cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal', 'detalles.producto', 'detalles.almacen'])
            ->findOrFail($id);

        return Pdf::loadView('ventas.pdf', compact('venta'))
            ->setPaper('a4', 'portrait')
            ->download('venta_' . $venta->id . '.pdf');
    }

    public function exportAllPdf(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'tipoVenta', 'caja', 'detalles.producto', 'detalles.almacen']);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();
        $pdf = Pdf::loadView('ventas.all-pdf', compact('ventas'))->setPaper('a4', 'landscape');

        return $pdf->download('ventas_reporte.pdf');
    }

    public function getCuentaAbierta($cliente_id)
    {
        $venta = Venta::where('cliente_id', $cliente_id)
            ->where('estado', 'cuenta_abierta')
            ->with('detalles.producto')
            ->latest()
            ->first();

        return response()->json($venta);
    }

    public function getStatsDia(Request $request)
    {
        $fecha  = $request->input('fecha', now()->toDateString());
        $sesion = $request->input('sesion_id');

        $query = Venta::whereDate('created_at', $fecha)->where('estado', 'completada');
        if ($sesion) {
            $query->where('sesion_caja_id', $sesion);
        }

        return response()->json([
            'total' => (float) $query->sum('total'),
            'count' => (int) $query->count(),
            'fecha' => $fecha,
        ]);
    }

    public function getVentasTurno($sesionId)
    {
        $ventas = Venta::with('cliente')
            ->where('sesion_caja_id', $sesionId)
            ->where('estado', 'completada')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'cliente_nombre'=> $v->cliente->nombre ?? 'Consumidor Final',
                'total'         => (float) $v->total,
                'metodo_pago'   => optional($v->pagos()->latest()->first())->metodo_pago ?? 'efectivo',
                'hora'          => $v->created_at->format('h:i A'),
                'ncf'           => $v->ncf,
                'encf'          => $v->encf,
            ]);

        return response()->json(['ventas' => $ventas]);
    }

    public function imprimir($id)
    {
        $venta = Venta::findOrFail($id);
        try {
            app(PrintService::class)->imprimir($venta);
            return response()->json(['success' => true, 'message' => 'Impresión enviada.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function facturar($id)
    {
        $venta = Venta::findOrFail($id);
        try {
            $ecfService = app(\App\Services\Ecf\EcfService::class);
            $ecf = $ecfService->generarEcf($venta);
            $firmado = $ecfService->firmar($ecf);
            $ecfService->enviar($firmado);
            return response()->json(['success' => true, 'message' => 'e-CF generado y enviado a DGII.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error al facturar: ' . $e->getMessage()], 500);
        }
    }
}
