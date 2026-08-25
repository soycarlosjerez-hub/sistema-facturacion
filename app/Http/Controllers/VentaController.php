<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Almacen;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\EcfDocumento;
use App\Models\Equipo;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\User;
use App\Models\Venta;
use App\Exports\VentasExport;
use App\Services\SaleService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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

        if (auth()->user()->can('ventas.view.own') && !auth()->user()->can('ventas.view')) {
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

        if ($data['sesiones']->isEmpty()) {
            return redirect()->route('cajas.index')
                ->with('error', 'Necesitas abrir una caja antes de vender.');
        }

        return view('ventas.create', $data);
    }

    public function store(StoreVentaRequest $request)
    {
        $sesionId = $request->input('sesion_caja_id');
        
        $isElevated = in_array(Auth::user()->role, ['admin', 'owner', 'admin-business', 'root'])
            || Auth::user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        if ($sesionId) {
            $sesion = SesionCaja::where('id', $sesionId)
                ->where('estado', 'abierta');
            if (!$isElevated) {
                $sesion->where('user_id', Auth::id());
            }
            $sesion = $sesion->first();
        } else {
            $sesion = SesionCaja::where('estado', 'abierta');
            if (!$isElevated) {
                $sesion->where('user_id', Auth::id());
            }
            $sesion = $sesion->latest('fecha_apertura')->first();
        }

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
            report($e);
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
        }
    }

    public function autorizarAdmin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $rolesAdmin = ['admin', 'admin-business', 'root', 'gerente'];

        $user = User::where('email', $request->email)->first();

        $esAdmin = $user && (
            in_array($user->role, $rolesAdmin)
            || $user->hasAnyRole($rolesAdmin)
        );

        if (!$user || !$esAdmin || !Hash::check($request->password, $user->password)) {
            Log::warning('Autorización admin rechazada para quitar ITBIS', [
                'email'     => $request->email,
                'user_id'   => Auth::id(),
                'tenant_id' => Auth::user()->business_instance_id,
            ]);

            return response()->json([
                'error' => 'Credenciales inválidas o el usuario no tiene rol de administrador.',
            ], 401);
        }

        $expira = now()->addMinutes(5);
        $token = Crypt::encryptString(json_encode([
            'email'     => $user->email,
            'tenant_id' => Auth::user()->business_instance_id,
            'exp'       => $expira->timestamp,
        ]));

        Log::info('Autorización admin emitida para quitar ITBIS', [
            'autorizado_por' => $user->email,
            'user_id'        => Auth::id(),
            'tenant_id'      => Auth::user()->business_instance_id,
        ]);

        return response()->json([
            'success' => true,
            'token'   => $token,
            'admin'   => $user->name,
            'expira'  => $expira->toDateTimeString(),
        ]);
    }

    public function show($id)
    {
        $venta = Venta::with([
            'cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal',
            'detalles.producto', 'detalles.obra', 'detalles.almacen'
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

        $productos = Producto::where('tenant_id', Auth::user()->business_instance_id)
            ->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('codigo_barras', $termino)
                  ->orWhere('codigo_barras', 'like', '%' . $termino . '%');
            })->orderBy('nombre')->limit(20)
            ->get(['id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'unidad_medida', 'imagen']);

        return response()->json($productos);
    }

    public function buscarPorCodigoBarras($codigo)
    {
        $producto = Producto::where('codigo_barras', $codigo)
            ->where('tenant_id', Auth::user()->business_instance_id)
            ->where('activo', true)
            ->first();
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
        $venta = Venta::with(['cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal', 'detalles.producto', 'detalles.obra', 'detalles.almacen'])
            ->where('tenant_id', Auth::user()->business_instance_id)
            ->findOrFail($id);

        return Pdf::loadView('ventas.pdf', compact('venta'))
            ->setPaper('a4', 'portrait')
            ->download('venta_' . $venta->id . '.pdf');
    }

    public function exportAllPdf(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'tipoVenta', 'caja', 'detalles.producto', 'detalles.almacen'])
            ->where('tenant_id', Auth::user()->business_instance_id);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $ventas = $query->orderBy('created_at', 'desc')->take(1000)->get();
        $pdf = Pdf::loadView('ventas.all-pdf', compact('ventas'))->setPaper('a4', 'landscape');

        return $pdf->download('ventas_reporte.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new VentasExport($request->cliente, $request->desde, $request->hasta),
            'ventas.xlsx'
        );
    }

    public function exportCsv(Request $request)
    {
        return Excel::download(
            new VentasExport($request->cliente, $request->desde, $request->hasta),
            'ventas.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
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

        $query = Venta::where('tenant_id', Auth::user()->business_instance_id)
            ->whereDate('created_at', $fecha)
            ->where('estado', 'completada');
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
        $ventas = Venta::with(['cliente', 'pagos'])
            ->where('sesion_caja_id', $sesionId)
            ->where('estado', 'completada')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'cliente_nombre'=> $v->cliente?->nombre ?? 'Consumidor Final',
                'total'         => (float) $v->total,
                'metodo_pago'   => $v->pagos->last()?->metodo_pago ?? 'efectivo',
                'hora'          => $v->created_at->format('h:i A'),
                'ncf'           => $v->ncf,
                'encf'          => $v->encf,
            ]);

        return response()->json(['ventas' => $ventas]);
    }

    public function ticket($id)
    {
        $venta = Venta::with(['cliente', 'usuario', 'detalles.producto', 'detalles.obra', 'caja', 'sucursal', 'pagos'])->findOrFail($id);
        return view('ventas.ticket', ['venta' => $venta]);
    }

    public function facturar(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('ventas.create');
        }

        $venta = Venta::with('cliente', 'detalles.producto', 'usuario')->findOrFail($id);

        if ($venta->detalles->isEmpty()) {
            return response()->json(['error' => 'La venta no tiene productos para facturar'], 422);
        }

        try {
            $this->saleService->procesarEcf($venta);

            $ecf = EcfDocumento::where('venta_id', $venta->id)
                ->whereNotNull('encf')
                ->orderByDesc('id')
                ->first();

            $estado = $ecf ? $ecf->estado : 'pendiente';
            $message = $estado === 'aprobado'
                ? 'e-CF aprobado por DGII.'
                : 'e-CF procesado (estado: ' . $estado . ').';

            return response()->json([
                'success' => true,
                'message' => $message,
                'encf'    => $ecf ? $ecf->encf : null,
                'estado'  => $estado,
            ]);
        } catch (\Throwable $e) {
            Log::error('Facturación DGII fallida', [
                'venta_id' => $id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Error al facturar. Contacte al administrador.'], 500);
        }
    }

    public function buscarEquipo(Request $request)
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        $facturacionModo = $tipo?->config['facturacion_modo'] ?? 'productos';

        if ($facturacionModo !== 'equipos') {
            return response()->json([]);
        }

        $term = $request->q ?? $request->termino ?? $request->get('search', '');

        if (empty($term)) {
            $equipos = Equipo::where('estado', 'disponible')
                ->where('tenant_id', $user->business_instance_id)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get(['id', 'serial_imei', 'serial_esn', 'marca', 'modelo', 'color',
                       'almacenamiento_gb', 'precio_venta', 'tipo_dispositivo']);
        } else {
            $equipos = Equipo::where(function($q) use ($term) {
                $q->where('serial_imei', 'like', "%{$term}%")
                  ->orWhere('serial_esn', 'like', "%{$term}%")
                  ->orWhere('marca', 'like', "%{$term}%")
                  ->orWhere('modelo', 'like', "%{$term}%")
                  ->orWhere('color', 'like', "%{$term}%");
            })
            ->where('estado', 'disponible')
            ->where('tenant_id', $user->business_instance_id)
            ->take(20)
            ->get(['id', 'serial_imei', 'serial_esn', 'marca', 'modelo', 'color',
                   'almacenamiento_gb', 'precio_venta', 'tipo_dispositivo']);
        }

        return response()->json($equipos->map(function($e) {
            return [
                'id' => 'equipo_' . $e->id,
                'equipo_id' => (int) $e->id,
                'label' => $e->marca . ' ' . $e->modelo . ' (' . $e->serial_imei . ')',
                'meta' => $e->color . ($e->almacenamiento_gb ? ' · ' . $e->almacenamiento_gb . 'GB' : '') . ' · ' . ucfirst($e->tipo_dispositivo),
                'precio' => (float) $e->precio_venta,
                'serial_imei' => $e->serial_imei,
            ];
        }));
    }

    public function buscarServicio(Request $request)
    {
        $user = Auth::user();
        $tipo = $user?->businessInstance?->businessType;
        $facturacionModo = $tipo?->config['facturacion_modo'] ?? 'productos';

        if ($facturacionModo !== 'productos_y_servicios') {
            return response()->json([]);
        }

        $term = $request->q ?? $request->termino ?? $request->get('search', '');

        $query = \App\Models\LavaderoServicio::where('tenant_id', $user->business_instance_id)
            ->where('activo', true);

        if (!empty($term)) {
            $query->where(function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('descripcion', 'like', "%{$term}%")
                  ->orWhere('categoria', 'like', "%{$term}%");
            });
        }

        $servicios = $query->orderBy('nombre')->take(20)->get([
            'id', 'nombre', 'descripcion', 'precio', 'precio_compra',
            'itbis_porcentaje', 'duracion_minutos', 'categoria', 'imagen'
        ]);

        return response()->json($servicios->map(function($s) {
            return [
                'id' => 'servicio_' . $s->id,
                'servicio_id' => (int) $s->id,
                'label' => $s->nombre,
                'meta' => ($s->categoria ? $s->categoria . ' · ' : '') .
                          ($s->duracion_minutos ? $s->duracion_minutos . ' min' : ''),
                'precio' => (float) $s->precio,
                'itbis_p' => (float) ($s->itbis_porcentaje ?? 18),
                'duracion' => $s->duracion_minutos,
                'categoria' => $s->categoria,
                'descripcion' => $s->descripcion,
            ];
        }));
    }
}
