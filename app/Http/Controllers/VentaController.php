<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\TipoVenta;
use App\Models\SesionCaja;
use App\Models\Caja;
use App\Support\RncValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use App\Services\NcfService;
use App\Services\Ecf\EcfService;

class VentaController extends Controller
{
    protected $ncfService;
    protected $ecfService;

    public function __construct(NcfService $ncfService, EcfService $ecfService)
    {
        $this->ncfService = $ncfService;
        $this->ecfService = $ecfService;
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
            $query->whereHas(
                'cliente',
                fn($q) =>
                $q->where('nombre', 'like', '%' . $request->cliente . '%')
            );
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
        $sesion = SesionCaja::with('caja')
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (! $sesion) {
            return redirect()->route('cajas.index')
                ->with('error', 'Necesitas abrir una caja antes de vender. Selecciona una caja y ábrela con tu fondo inicial.');
        }

        $cajas = Caja::activas()->orderBy('nombre')->get();

        $clienteConsumidorFinal = Cliente::firstOrCreate(
            ['nombre' => 'Consumidor Final'],
            ['limite_credito' => 0, 'balance_pendiente' => 0, 'tipo_cliente' => 'consumo']
        );

        $clientes   = Cliente::orderBy('nombre')->get();
        $tiposVenta = TipoVenta::orderBy('nombre')->get();
        $tipoVentaDefault = $tiposVenta->firstWhere('nombre', 'Contado') ?? $tiposVenta->first();
        $productos  = Producto::orderBy('nombre')
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen')
            ->get()
            ->map(function ($p) {
                $p->stock_disponible = $p->stock;
                $p->imagen_url = $p->imagen_url;
                return $p;
            });

        $almacenes  = Almacen::orderBy('nombre')->get();

        $stockPorProductoAlmacen = AlmacenMovimiento::query()
            ->selectRaw('producto_id, almacen_id, SUM(CASE WHEN tipo = "entrada" THEN cantidad ELSE -cantidad END) as stock')
            ->groupBy('producto_id', 'almacen_id')
            ->get();
        $stocks = [];
        foreach ($stockPorProductoAlmacen as $row) {
            $stocks[$row->producto_id][$row->almacen_id] = (int) $row->stock;
        }
        foreach ($productos as $producto) {
            $stocks[$producto->id] = $stocks[$producto->id] ?? [];
        }

        $ncfSequences = \App\Models\NcfSequence::where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->get();

        // Pre-serialize data for the POS view (avoids Blade @json mangling complex arrays)
        $productosJs = $productos->map(function ($p) {
            return [
                'id'            => (int) $p->id,
                'nombre'        => $p->nombre,
                'codigo_barras' => $p->codigo_barras,
                'precio'        => (float) $p->precio,
                'precio_compra' => (float) ($p->precio_compra ?? 0),
                'itbis_p'       => (float) ($p->itbis_porcentaje ?? 18),
                'stock'         => (int) $p->stock,
                'ventas_count'  => (int) ($p->ventas_count ?? 0),
                'unidad_medida' => $p->unidad_medida ?? 'Unidad',
                'imagen_url'    => $p->imagen_url,
            ];
        })->values()->all();

        $clientesJs = $clientes->map(function ($c) use ($clienteConsumidorFinal) {
            return [
                'id'       => (int) $c->id,
                'nombre'   => $c->nombre,
                'tipo'     => $c->tipo_cliente ?? 'consumo',
                'deuda'    => (float) ($c->balance_pendiente ?? 0),
                'es_final' => $c->id == $clienteConsumidorFinal->id,
            ];
        })->values()->all();

        return view('ventas.create', compact(
            'clientes', 'tiposVenta', 'productos', 'almacenes', 'stocks', 'ncfSequences',
            'sesion', 'cajas', 'clienteConsumidorFinal', 'tipoVentaDefault',
            'productosJs', 'clientesJs'
        ));
    }

    public function store(Request $request)
    {
        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (! $sesion) {
            return back()->with('error', 'Tu caja se cerró. No se puede registrar la venta.');
        }

        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_venta_id' => 'required|exists:tipos_ventas,id',
            'producto_id' => 'required|array|min:1',
            'producto_id.*' => 'exists:productos,id',
            'almacen_id' => 'required|array|min:1',
            'almacen_id.*' => 'exists:almacenes,id',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'integer|min:1',
            'precio' => 'required|array|min:1',
            'precio.*' => 'numeric|min:0',
            'subtotal' => 'required|array|min:1',
            'subtotal.*' => 'numeric|min:0',
            'descuento' => 'nullable|array',
            'descuento.*' => 'numeric|min:0',
            'descuento_tipo' => 'nullable|array',
            'descuento_tipo.*' => 'in:monto,porcentaje',
            'total' => 'required|numeric|min:0',
            'metodo_pago' => 'nullable|string|in:efectivo,tarjeta,transferencia,fiado,cuenta_abierta,mixto',
        ]);

        DB::beginTransaction();

        try {
            $ncf = null;
            if ($request->filled('ncf_tipo')) {
                $ncf = $this->ncfService->getNextNcf($request->ncf_tipo);
            }

            $metodo = $request->metodo_pago ?? 'efectivo';
            $estado = 'completada';
            $ventaExistente = null;

            if ($metodo === 'fiado') {
                $estado = 'pendiente';
            } elseif ($metodo === 'cuenta_abierta') {
                $estado = 'cuenta_abierta';
                $ventaExistente = Venta::where('cliente_id', $request->cliente_id)
                    ->where('estado', 'cuenta_abierta')
                    ->latest()
                    ->first();
            }

            if ($ventaExistente) {
                $venta = $ventaExistente;
                $venta->increment('subtotal', $request->subtotal_final ?? array_sum($request->subtotal));
                $venta->increment('impuestos', $request->impuestos ?? 0);
                $venta->increment('total', $request->total);
                $venta->update(['fecha' => now()]);
            } else {
                $tipoComprobante = $request->input('tipo_comprobante', 'ncf');

                $venta = Venta::create([
                    'ncf'              => $ncf,
                    'ncf_tipo'         => $request->ncf_tipo,
                    'tipo_comprobante' => $tipoComprobante,
                    'encf'             => null,
                    'user_id'          => Auth::id(),
                    'sucursal_id'      => session('sucursal_id'),
                    'caja_id'          => $sesion->caja_id,
                    'sesion_caja_id'   => $sesion->id,
                    'cliente_id'       => $request->cliente_id,
                    'tipo_venta_id'    => $request->tipo_venta_id,
                    'fecha'            => now(),
                    'impuestos'        => $request->impuestos ?? 0,
                     'descuento' => is_array($request->descuento) ? ($request->descuento[0] ?? 0) : ($request->descuento ?? 0),
                    'subtotal'         => $request->subtotal_final ?? array_sum($request->subtotal),
                    'total'            => $request->total,
                    'estado'           => $estado,
                ]);

                if ($tipoComprobante === 'ecf') {
                    if ($venta->cliente_id) {
                        $cliente = $venta->cliente;
                        if ($cliente && !empty($cliente->rnc_cedula)) {
                            $tipoDoc = $cliente->tipo_documento ?? RncValidator::inferirTipo($cliente->rnc_cedula);
                            $valido = RncValidator::validar($cliente->rnc_cedula, $tipoDoc);
                            if (!$valido) {
                                throw new \Exception("El RNC/Cédula del cliente ({$cliente->rnc_cedula}) no es válido según DGII. Corrija los datos del cliente antes de emitir un e-CF.");
                            }
                        } elseif ($cliente && in_array($venta->tipo_ecf ?? '', ['E31', 'E44', 'E45'])) {
                            throw new \Exception("Los e-CF tipo Crédito Fiscal requieren un cliente con RNC válido.");
                        }
                    }
                    try {
                        $ecf = $this->ecfService->generarEcf($venta);
                        $ecfFirmado = $this->ecfService->firmar($ecf);
                        $this->ecfService->enviar($ecfFirmado);
                    } catch (\Throwable $e) {
                        \Log::warning('No se pudo generar e-CF para la venta #' . $venta->id . ': ' . $e->getMessage());
                    }
                }
            }

            foreach ($request->producto_id as $key => $producto_id) {
                $almacenId = $request->almacen_id[$key] ?? 1;
                $cantidad = $request->cantidad[$key];

                $disponible = $this->stockDisponible($producto_id, $almacenId);
                if ($disponible < $cantidad) {
                    $p = Producto::find($producto_id);
                    throw new \Exception("Stock insuficiente para: {$p->nombre} (Disponible: {$disponible})");
                }

                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $producto_id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $request->precio[$key],
                    'subtotal'        => $request->subtotal[$key],
                    'almacen_id'      => $almacenId,
                ]);

                AlmacenMovimiento::create([
                    'producto_id' => $producto_id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'nota'        => 'Venta #' . $venta->id . ($ventaExistente ? ' (Adición)' : ''),
                    'user_id'     => Auth::id(),
                ]);

                $producto = Producto::find($producto_id);
                $producto->decrement('stock', $cantidad);
                $producto->increment('ventas_count', $cantidad);
            }

            if ($estado === 'pendiente' || $estado === 'cuenta_abierta') {
                $cliente = Cliente::find($request->cliente_id);
                $cliente->increment('balance_pendiente', $request->total);
            } else {
                \App\Models\Pago::create([
                    'venta_id'        => $venta->id,
                    'caja_id'         => $sesion->caja_id,
                    'sesion_caja_id'  => $sesion->id,
                    'monto'           => $request->total,
                    'metodo_pago'     => $metodo,
                    'nota'            => 'Pago automático (Venta ' . ucfirst($metodo) . ')',
                    'fecha_pago'      => now(),
                ]);

                if ($metodo === 'efectivo') {
                    $sesion->increment('ventas_efectivo', $request->total);
                } elseif ($metodo === 'tarjeta') {
                    $sesion->increment('ventas_tarjeta', $request->total);
                } elseif ($metodo === 'transferencia') {
                    $sesion->increment('ventas_transferencia', $request->total);
                }
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta->id)
                ->with('success', 'Venta registrada en ' . $sesion->caja->nombre . ($ventaExistente ? ' (Agregada a Cuenta Abierta)' : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage())->withInput();
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
            })
            ->orderBy('nombre')
            ->limit(20)
            ->get(['id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'unidad_medida', 'imagen']);

        return response()->json($productos);
    }

    public function buscarPorCodigoBarras($codigo)
    {
        $producto = Producto::where('codigo_barras', $codigo)->first();

        if (! $producto) {
            return response()->json(['encontrado' => false], 404);
        }

        return response()->json(['encontrado' => true, 'producto' => $producto]);
    }

    public function cambiarCaja(Request $request)
    {
        $data = $request->validate([
            'caja_id' => 'required|exists:cajas,id',
        ]);

        $caja = Caja::findOrFail($data['caja_id']);
        $sesion = $caja->sesionActiva();

        if (! $sesion || $sesion->user_id !== Auth::id()) {
            return back()->with('error', 'No tienes una sesión abierta en esa caja.');
        }

        return redirect()->route('ventas.create');
    }

    private function stockDisponible($productoId, $almacenId)
    {
        $entrada = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->where('tipo', 'entrada')
            ->sum('cantidad');
        $salida = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->where('tipo', 'salida')
            ->sum('cantidad');
        return $entrada - $salida;
    }

    public function destroy(Request $request, $id)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Solo los administradores pueden anular ventas.');
        }

        $request->validate([
            'motivo'   => 'required|string|min:5|max:500',
            'confirmar'=> 'required|accepted',
        ]);

        DB::beginTransaction();

        try {
            $venta = Venta::with('detalles')->findOrFail($id);
            $motivo = trim($request->motivo);

            foreach ($venta->detalles as $detalle) {
                $almacenId = ($detalle->almacen_id > 0) ? $detalle->almacen_id : 1;
                AlmacenMovimiento::create([
                    'producto_id' => $detalle->producto_id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'entrada',
                    'cantidad'    => $detalle->cantidad,
                    'nota'        => 'ANULACIÓN Venta #' . $venta->id . ' | Motivo: ' . $motivo,
                    'user_id'     => auth()->id(),
                ]);
            }

            if ($venta->cliente_id && in_array($venta->estado, ['pendiente', 'cuenta_abierta'])) {
                $cliente = Cliente::find($venta->cliente_id);
                if ($cliente) {
                    $montoDeuda = $venta->total - $venta->montoPagado();
                    if ($montoDeuda > 0) {
                        $cliente->decrement('balance_pendiente', $montoDeuda);
                    }
                }
            }

            if ($venta->sesion_caja_id) {
                $sesion = SesionCaja::find($venta->sesion_caja_id);
                if ($sesion) {
                    foreach ($venta->pagos as $pago) {
                        if ($pago->metodo_pago === 'efectivo')         $sesion->decrement('ventas_efectivo', $pago->monto);
                        elseif ($pago->metodo_pago === 'tarjeta')      $sesion->decrement('ventas_tarjeta', $pago->monto);
                        elseif ($pago->metodo_pago === 'transferencia')$sesion->decrement('ventas_transferencia', $pago->monto);
                    }
                }
            }

            \Log::info('Venta anulada', [
                'venta_id' => $venta->id,
                'total'    => $venta->total,
                'motivo'   => $motivo,
                'user_id'  => auth()->id(),
            ]);

            $venta->detalles()->delete();
            $venta->pagos()->delete();
            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT) . ' anulada. Stock regresado al inventario.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Error al anular: ' . $e->getMessage());
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

    public function exportPdf($id)
    {
        $venta = Venta::with([
            'cliente', 'usuario', 'tipoVenta', 'caja', 'sucursal',
            'detalles.producto', 'detalles.almacen'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('ventas.pdf', compact('venta'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('venta_' . $venta->id . '.pdf');
    }

    public function exportAllPdf(Request $request)
    {
        $query = Venta::with([
            'cliente', 'usuario', 'tipoVenta', 'caja',
            'detalles.producto', 'detalles.almacen'
        ]);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%');
            });
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $ventas = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('ventas.all-pdf', compact('ventas'))
            ->setPaper('a4', 'landscape');

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

        $query = Venta::whereDate('created_at', $fecha)
            ->where('estado', 'completada');

        if ($sesion) {
            $query->where('sesion_caja_id', $sesion);
        }

        $total = (float) $query->sum('total');
        $count = (int) $query->count();

        return response()->json([
            'total' => $total,
            'count' => $count,
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
            ->map(function ($v) {
                return [
                    'id'            => $v->id,
                    'cliente_nombre'=> $v->cliente->nombre ?? 'Consumidor Final',
                    'total'         => (float) $v->total,
                    'metodo_pago'   => optional($v->pagos()->latest()->first())->metodo_pago ?? 'efectivo',
                    'hora'          => $v->created_at->format('h:i A'),
                    'ncf'           => $v->ncf,
                    'encf'          => $v->encf,
                ];
            });

        return response()->json(['ventas' => $ventas]);
    }
}
