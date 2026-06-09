<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Caja;
use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\MesaCategoria;
use App\Models\Reservacion;
use App\Models\SesionCaja;
use App\Models\Pago;
use App\Models\WaitlistEntry;
use App\Services\Ecf\EcfService;
use App\Services\PrintService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RestauranteController extends Controller
{
    public function __construct(
        protected EcfService $ecfService,
        protected PrintService $printService,
    ) {}

    public function index()
    {
        $this->autoOcuparMesasReservadas();

        $mesas = Mesa::deSucursal()->with('ordenActiva')->orderBy('numero')->get();
        $cajas = Caja::where('activo', true)->orderBy('nombre')->get();
        $sesionActiva = SesionCaja::with('caja')
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        return view('restaurante.index', compact('mesas', 'cajas', 'sesionActiva'));
    }

    private function autoOcuparMesasReservadas()
    {
        $reservaciones = Reservacion::whereIn('estado', ['pendiente', 'confirmada'])
            ->where('fecha_hora', '<=', now())
            ->whereHas('mesa', fn($q) => $q->where('estado', 'reservada')->whereDoesntHave('ordenActiva'))
            ->get();

        foreach ($reservaciones as $reservacion) {
            DB::beginTransaction();
            try {
                $mesa = $reservacion->mesa;

                $cliente = $reservacion->cliente ?? Cliente::firstOrCreate(
                    ['nombre' => $reservacion->cliente_nombre],
                    ['telefono' => $reservacion->cliente_telefono, 'email' => $reservacion->cliente_email]
                );

                $sesion = SesionCaja::where('user_id', Auth::id())
                    ->where('estado', 'abierta')
                    ->latest('fecha_apertura')
                    ->first();

                Venta::create([
                    'user_id'        => Auth::id(),
                    'sucursal_id'    => session('sucursal_id'),
                    'mesa_id'        => $mesa->id,
                    'caja_id'        => $sesion?->caja_id,
                    'sesion_caja_id' => $sesion?->id,
                    'cliente_id'     => $cliente->id,
                    'tipo_venta_id'  => 1,
                    'fecha'          => now(),
                    'subtotal'       => 0,
                    'impuestos'      => 0,
                    'total'          => 0,
                    'estado'         => 'abierta',
                    'tipo_orden'     => 'mesa',
                    'notas'          => 'Reservación automática - ' . $reservacion->cliente_nombre,
                ]);

                $mesa->update(['estado' => 'ocupada']);
                $reservacion->update(['estado' => 'cumplida']);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error auto-ocupando mesa reservada: ' . $e->getMessage());
            }
        }
    }

    public function getMesa(Mesa $mesa)
    {
        $orden = $mesa->ordenActiva;
        if ($orden) {
            $orden->load('detalles.producto', 'cliente', 'usuario');
        }
        $reservacion = $mesa->reservacion;
        return response()->json([
            'mesa'        => $mesa,
            'orden'       => $orden,
            'reservacion' => $reservacion,
        ]);
    }

    public function catalogo()
    {
        $productos = Producto::where('stock', '>', 0)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'codigo_barras', 'imagen', 'categoria_id']);

        $categorias = Categoria::orderBy('nombre')->get(['id', 'nombre']);

        return response()->json(['productos' => $productos, 'categorias' => $categorias]);
    }

    public function buscarProducto(Request $request)
    {
        $termino = $request->input('q');
        if (strlen($termino) < 2) {
            return response()->json([]);
        }
        $productos = Producto::where(function ($q) use ($termino) {
            $q->where('nombre', 'like', "%{$termino}%")
              ->orWhere('codigo_barras', 'like', "%{$termino}%");
        })
        ->where('stock', '>', 0)
        ->limit(20)
        ->get(['id', 'nombre', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'codigo_barras', 'imagen', 'categoria_id']);

        return response()->json($productos);
    }

    public function abrirMesa(Request $request, Mesa $mesa)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'tipo_orden' => 'nullable|in:mesa,delivery,para_llevar',
        ]);

        if ($mesa->estado !== 'disponible' && $mesa->estado !== 'reservada') {
            return response()->json(['error' => 'La mesa no está disponible'], 422);
        }

        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            return response()->json(['error' => 'No tienes una sesión de caja abierta'], 422);
        }

        $tipoOrden = $request->tipo_orden ?? 'mesa';
        $servicioPorcentaje = (float) config('app.servicio_porcentaje', 0);
        $servicioMinPersonas = (int) config('app.servicio_min_personas', 8);
        $aplicarServicio = $servicioPorcentaje > 0 && $mesa->capacidad >= $servicioMinPersonas;

        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'user_id'        => Auth::id(),
                'sucursal_id'    => session('sucursal_id'),
                'mesa_id'        => $mesa->id,
                'caja_id'        => $sesion->caja_id,
                'sesion_caja_id' => $sesion->id,
                'cliente_id'     => $request->cliente_id ?? Cliente::consumidorFinal()->id,
                'tipo_venta_id'  => 1,
                'fecha'          => now(),
                'subtotal'       => 0,
                'impuestos'      => 0,
                'total'          => 0,
                'estado'         => 'abierta',
                'tipo_orden'     => $tipoOrden,
            ]);

            $mesa->update(['estado' => 'ocupada']);

            DB::commit();
            return response()->json(['orden' => $venta->load('detalles.producto')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agregarItem(Request $request, Mesa $mesa)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad'    => 'required|integer|min:1',
            'notas'       => 'nullable|string|max:200',
            'curso'       => 'nullable|in:entrada,fuerte,postre,bebida',
        ]);

        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return response()->json(['error' => 'La mesa no tiene una orden abierta'], 422);
        }

        $sucursalId = session('sucursal_id');
        $almacen = Almacen::where('sucursal_id', $sucursalId)->first();
        $almacenId = $almacen?->id ?? 1;

        $producto = Producto::findOrFail($request->producto_id);
        $cantidad = $request->cantidad;
        $notas = $request->notas;
        $curso = $request->curso ?? 'fuerte';

        // Verificar si ya existe el mismo producto con mismas notas y curso en la orden
        $detalleExistente = VentaDetalle::where('venta_id', $orden->id)
            ->where('producto_id', $producto->id)
            ->where('notas', $notas)
            ->where('curso', $curso)
            ->first();

        DB::beginTransaction();
        try {
            if ($detalleExistente) {
                $nuevaCantidad = $detalleExistente->cantidad + $cantidad;
                if ($producto->stock < $cantidad) {
                    return response()->json(['error' => "Stock insuficiente. Disponible: {$producto->stock}, solicitado: {$cantidad}"], 422);
                }

                $detalleExistente->cantidad = $nuevaCantidad;
                $detalleExistente->subtotal = $producto->precio * $nuevaCantidad;
                $detalleExistente->save();

                $itbisItem = ($producto->itbis_porcentaje ?? 0) / 100 * $producto->precio * $cantidad;
                $orden->increment('subtotal', $producto->precio * $cantidad);
                $orden->increment('impuestos', $itbisItem);
                $orden->increment('total', ($producto->precio * $cantidad) + $itbisItem);

                $producto->decrement('stock', $cantidad);

                AlmacenMovimiento::create([
                    'producto_id' => $producto->id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'nota'        => 'Pedido restaurante - Mesa #' . $mesa->numero,
                    'user_id'     => Auth::id(),
                ]);

                $detalle = $detalleExistente->fresh();
            } else {
                if ($producto->stock < $cantidad) {
                    return response()->json(['error' => "Stock insuficiente. Disponible: {$producto->stock}, solicitado: {$cantidad}"], 422);
                }

                $detalle = VentaDetalle::create([
                    'venta_id'        => $orden->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $producto->precio * $cantidad,
                    'almacen_id'      => $almacenId,
                    'notas'           => $notas,
                    'curso'           => $curso,
                ]);

                $itbisItem = ($producto->itbis_porcentaje ?? 0) / 100 * $producto->precio * $cantidad;
                $orden->increment('subtotal', $producto->precio * $cantidad);
                $orden->increment('impuestos', $itbisItem);
                $orden->increment('total', ($producto->precio * $cantidad) + $itbisItem);

                $producto->decrement('stock', $cantidad);

                AlmacenMovimiento::create([
                    'producto_id' => $producto->id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'nota'        => 'Pedido restaurante - Mesa #' . $mesa->numero,
                    'user_id'     => Auth::id(),
                ]);
            }

            DB::commit();
            return response()->json([
                'orden'     => $orden->fresh()->load('detalles.producto'),
                'detalle'   => $detalle->load('producto'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function quitarItem(Request $request, Mesa $mesa, VentaDetalle $detalle)
    {
        $orden = $mesa->ordenActiva;
        if (!$orden || $detalle->venta_id !== $orden->id) {
            return response()->json(['error' => 'El detalle no pertenece a esta orden'], 422);
        }

        DB::beginTransaction();
        try {
            $subtotal = $detalle->subtotal;
            $itbisItem = ($detalle->producto->itbis_porcentaje ?? 0) / 100 * $subtotal;

            $orden->decrement('subtotal', $subtotal);
            $orden->decrement('impuestos', $itbisItem);
            $orden->decrement('total', $subtotal + $itbisItem);

            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            $detalle->delete();

            DB::commit();
            return response()->json(['orden' => $orden->fresh()->load('detalles.producto')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cobrar(Request $request, Mesa $mesa)
    {
        $request->validate([
            'metodo_pago'          => 'required|string|in:efectivo,tarjeta,transferencia,mixto',
            'monto_recibido'       => 'nullable|numeric|min:0',
            'monto_tarjeta'        => 'nullable|numeric|min:0',
            'monto_transferencia'  => 'nullable|numeric|min:0',
            'propina'              => 'nullable|numeric|min:0',
            'split'                => 'nullable|boolean',
            'personas'             => 'nullable|integer|min:2|max:10|required_if:split,true',
            'totales'              => 'nullable|array|required_if:split,true',
            'items_por_persona'    => 'nullable|array',
        ]);

        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return response()->json(['error' => 'La mesa no tiene una orden abierta'], 422);
        }

        $sesion = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            return response()->json(['error' => 'No tienes una sesión de caja abierta'], 422);
        }

        $metodo = $request->metodo_pago;

        $propina = (float)($request->propina ?? 0);
        $totalConPropina = $orden->total + $propina;
        $isSplit = $request->boolean('split');

        // Validar que los pagos cubran el total
        if ($metodo === 'mixto' && !$isSplit) {
            $sumaPagos = (float)($request->monto_recibido ?? 0)
                       + (float)($request->monto_tarjeta ?? 0)
                       + (float)($request->monto_transferencia ?? 0);
            if ($sumaPagos < $totalConPropina) {
                return response()->json(['error' => 'La suma de los pagos (RD$ ' . number_format($sumaPagos, 2) . ') no cubre el total de RD$ ' . number_format($totalConPropina, 2)], 422);
            }
        }

        DB::beginTransaction();
        try {
            $splitNotas = '';
            if ($isSplit) {
                $totales = $request->totales ?? [];
                $splitNotas = 'Cuenta dividida en ' . ($request->personas ?? 2) . ' personas: ' . implode(', ', array_map(fn($t) => 'RD$ ' . number_format((float)$t, 2), $totales));
            }

            $orden->update([
                'estado'  => 'completada',
                'fecha'   => now(),
                'propina' => $propina,
                'total'   => $totalConPropina,
                'notas'   => $splitNotas ? ($orden->notas ? $orden->notas . ' | ' . $splitNotas : $splitNotas) : $orden->notas,
            ]);

            $mesa->update(['estado' => 'disponible']);

            if ($metodo === 'mixto') {
                $montos = [
                    ['metodo' => 'efectivo', 'monto' => $request->monto_recibido ?? 0],
                    ['metodo' => 'tarjeta', 'monto' => $request->monto_tarjeta ?? 0],
                    ['metodo' => 'transferencia', 'monto' => $request->monto_transferencia ?? 0],
                ];
                foreach ($montos as $pago) {
                    if ($pago['monto'] > 0) {
                        Pago::create([
                            'venta_id'       => $orden->id,
                            'caja_id'        => $sesion->caja_id,
                            'sesion_caja_id' => $sesion->id,
                            'monto'          => $pago['monto'],
                            'metodo_pago'    => $pago['metodo'],
                            'nota'           => 'Pago restaurante - Mesa #' . $mesa->numero,
                            'fecha_pago'     => now(),
                        ]);
                    }
                }
            } else {
                Pago::create([
                    'venta_id'       => $orden->id,
                    'caja_id'        => $sesion->caja_id,
                    'sesion_caja_id' => $sesion->id,
                    'monto'          => $orden->total,
                    'metodo_pago'    => $metodo,
                    'nota'           => 'Pago restaurante - Mesa #' . $mesa->numero,
                    'fecha_pago'     => now(),
                ]);
            }

            DB::commit();

            $orden->load('detalles.producto', 'cliente');

            return response()->json([
                'success' => true,
                'venta'   => [
                    'id'           => $orden->id,
                    'total'        => $orden->total,
                    'mesa_numero'  => $mesa->numero,
                    'mesa_nombre'  => $mesa->nombre ?? 'Mesa ' . $mesa->numero,
                    'metodo_pago'  => $metodo,
                    'cliente'      => $orden->cliente?->nombre ?? 'Consumidor Final',
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function facturar(Request $request, Mesa $mesa)
    {
        $ventaId = $request->input('venta_id');
        $venta = Venta::with('cliente', 'detalles.producto')->findOrFail($ventaId);

        if ($venta->mesa_id !== $mesa->id) {
            return response()->json(['error' => 'La venta no pertenece a esta mesa'], 422);
        }

        if ($venta->estado !== 'completada') {
            return response()->json(['error' => 'La orden debe estar pagada antes de facturar'], 422);
        }

        if ($venta->ecf) {
            return response()->json(['error' => 'Esta venta ya tiene un e-CF generado', 'ecf_id' => $venta->ecf->id], 422);
        }

        try {
            DB::beginTransaction();
            $venta->update(['tipo_comprobante' => 'ecf']);

            $ecf = $this->ecfService->generarEcf($venta);
            $firmado = $this->ecfService->firmar($ecf);
            $this->ecfService->enviar($firmado);
            DB::commit();

            return response()->json([
                'success' => true,
                'encf'    => $ecf->encf,
                'ecf_id'  => $ecf->id,
                'message' => "e-CF {$ecf->encf} generado y enviado a DGII",
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error facturando orden restaurante: ' . $e->getMessage());
            return response()->json(['error' => 'Error al facturar: ' . $e->getMessage()], 500);
        }
    }

    public function cajasDisponibles()
    {
        $cajas = Caja::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'codigo', 'estado']);
        return response()->json(['cajas' => $cajas]);
    }

    public function sesionActiva()
    {
        $sesion = SesionCaja::with('caja')
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        return response()->json(['sesion' => $sesion]);
    }

    public function abrirCaja(Request $request)
    {
        $data = $request->validate([
            'caja_id'       => 'required|exists:cajas,id',
            'monto_inicial' => 'required|numeric|min:0',
        ]);

        $caja = Caja::findOrFail($data['caja_id']);

        if (!$caja->activo) {
            return response()->json(['error' => 'Esta caja está inactiva'], 422);
        }
        if ($caja->estado === 'abierta') {
            return response()->json(['error' => 'La caja ya está abierta'], 422);
        }

        $sesionActiva = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->first();
        if ($sesionActiva) {
            return response()->json(['error' => 'Ya tienes una sesión abierta en ' . $sesionActiva->caja->nombre], 422);
        }

        DB::beginTransaction();
        try {
            $sesion = SesionCaja::create([
                'caja_id'        => $caja->id,
                'user_id'        => Auth::id(),
                'fecha_apertura' => now(),
                'monto_inicial'  => $data['monto_inicial'],
                'estado'         => 'abierta',
            ]);
            $caja->update(['estado' => 'abierta']);
            DB::commit();

            return response()->json([
                'success' => true,
                'sesion'  => $sesion->load('caja'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function crearCaja(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:100',
            'codigo'    => 'nullable|string|max:20|unique:cajas,codigo',
            'ubicacion' => 'nullable|string|max:100',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['activo'] = true;
        $data['estado'] = 'cerrada';

        $caja = Caja::create($data);

        return response()->json(['success' => true, 'caja' => $caja]);
    }

    public function ticket(Mesa $mesa, Request $request)
    {
        $ventaId = $request->input('venta_id');
        $venta = Venta::with('detalles.producto', 'cliente', 'pagos', 'mesa')->findOrFail($ventaId);
        $empresa = (object) config('app.empresa', []);
        $paper = (int) $request->get('paper', 80);
        return view('restaurante.ticket', compact('venta', 'mesa', 'empresa', 'paper'));
    }

    public function ticketText(Mesa $mesa, Request $request)
    {
        $ventaId = $request->input('venta_id');
        $venta = Venta::with('detalles.producto', 'cliente', 'pagos', 'mesa')->findOrFail($ventaId);
        $empresa = (object) config('app.empresa', []);
        $paper = (int) $request->get('paper', 80);
        $text = $this->printService->renderVentaTicket($venta, $empresa, $paper);
        $text = "MESA: {$mesa->nombre}\n" . $text;
        return response($text, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Content-Disposition', "inline; filename=ticket-mesa-{$mesa->numero}.txt");
    }

    public function cambiarEstado(Request $request, Mesa $mesa)
    {
        $request->validate([
            'estado' => 'required|string|in:disponible,ocupada,reservada,inactiva',
        ]);

        $mesa->update(['estado' => $request->estado]);

        return response()->json(['success' => true, 'mesa' => $mesa]);
    }

    public function mesasIndex()
    {
        $mesas = Mesa::deSucursal()->with('categoria')->orderBy('numero')->get();
        $categorias = MesaCategoria::orderBy('nombre')->get();

        return view('restaurante.mesas', compact('mesas', 'categorias'));
    }

    public function mesaShow(Mesa $mesa)
    {
        return response()->json($mesa->load('categoria'));
    }

    public function storeMesa(Request $request)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion'    => 'nullable|string|max:100',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['estado'] = 'disponible';

        Mesa::create($data);

        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa agregada correctamente.');
    }

    public function updateMesa(Request $request, Mesa $mesa)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion'    => 'nullable|string|max:100',
            'estado'       => 'required|string|in:disponible,ocupada,reservada,inactiva',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
        ]);

        $mesa->update($data);

        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa actualizada.');
    }

    public function destroyMesa(Mesa $mesa)
    {
        if ($mesa->estado === 'ocupada') {
            return back()->with('error', 'No se puede eliminar una mesa ocupada.');
        }
        $mesa->delete();
        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa eliminada.');
    }

    public function anularOrden(Request $request, Mesa $mesa)
    {
        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return response()->json(['error' => 'No hay orden activa'], 422);
        }

        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');
        $reason = $request->input('motivo', 'Anulación manual');

        if (!$isAdmin && $orden->total > 500) {
            return response()->json(['error' => 'Se requiere autorización de administrador para anular órdenes mayores a RD$500'], 422);
        }

        DB::beginTransaction();
        try {
            // Devolver stock
            foreach ($orden->detalles as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
                AlmacenMovimiento::create([
                    'producto_id' => $detalle->producto_id,
                    'almacen_id'  => $detalle->almacen_id,
                    'tipo'        => 'entrada',
                    'cantidad'    => $detalle->cantidad,
                    'nota'        => 'Anulación orden Mesa #' . $mesa->numero . ': ' . $reason,
                    'user_id'     => Auth::id(),
                ]);
            }

            $orden->update(['estado' => 'anulada', 'notas' => $reason]);
            $mesa->update(['estado' => 'disponible']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al anular: ' . $e->getMessage()], 500);
        }

        return response()->json(['success' => true]);
    }

    public function aplicarDescuento(Request $request, Mesa $mesa)
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        $request->validate([
            'tipo'   => 'required|in:porcentaje,monto',
            'valor'  => 'required|numeric|min:0',
            'motivo' => 'required|string|max:200',
        ]);

        $orden = $mesa->ordenActiva;
        if (!$orden) {
            return response()->json(['error' => 'No hay orden activa'], 422);
        }

        $valor = (float) $request->valor;
        $tipo = $request->tipo;

        if ($tipo === 'porcentaje') {
            if ($valor > 50 && !$isAdmin) {
                return response()->json(['error' => 'Descuento mayor a 50% requiere autorización de administrador'], 422);
            }
            $descuento = $orden->subtotal * ($valor / 100);
        } else {
            if ($valor > $orden->subtotal) {
                return response()->json(['error' => 'El descuento no puede exceder el subtotal'], 422);
            }
            $maxAuto = $orden->subtotal * 0.3;
            if ($valor > $maxAuto && !$isAdmin) {
                return response()->json(['error' => 'Descuento mayor a 30% del subtotal requiere autorización de administrador'], 422);
            }
            $descuento = $valor;
        }

        $descuento = round($descuento, 2);
        $nuevoSubtotal = round($orden->subtotal - $descuento, 2);
        $nuevoTotal = round($nuevoSubtotal + $orden->impuestos, 2);

        $orden->update([
            'subtotal'         => $nuevoSubtotal,
            'total'            => $nuevoTotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $tipo,
            'descuento_motivo' => $request->motivo,
        ]);

        $orden->load('detalles.producto', 'cliente', 'usuario');
        return response()->json(['success' => true, 'orden' => $orden]);
    }

    public function trasladarMesa(Request $request, Mesa $mesa)
    {
        $request->validate(['destino_id' => 'required|exists:mesas,id|different:mesa']);

        $destino = Mesa::findOrFail($request->destino_id);
        $orden = $mesa->ordenActiva;

        if (!$orden) {
            return response()->json(['error' => 'La mesa origen no tiene orden activa'], 422);
        }
        if ($destino->ordenActiva) {
            return response()->json(['error' => 'La mesa destino ya tiene una orden activa'], 422);
        }

        $orden->update(['mesa_id' => $destino->id]);
        $mesa->update(['estado' => 'disponible']);
        $destino->update(['estado' => 'ocupada']);

        return response()->json(['success' => true, 'orden' => $orden->fresh(['detalles.producto', 'cliente', 'usuario'])]);
    }

    public function historialMesa(Mesa $mesa)
    {
        $ordenes = Venta::where('mesa_id', $mesa->id)
            ->where('estado', '!=', 'abierta')
            ->with('detalles.producto', 'pagos', 'cliente')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $html = view('restaurante._historial', compact('ordenes', 'mesa'))->render();
        return response()->json(['html' => $html]);
    }

    // Reservaciones
    public function reservacionesIndex()
    {
        $query = Reservacion::with('mesa', 'user')->deSucursal();

        if ($busqueda = request('busqueda')) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('cliente_nombre', 'like', "%{$busqueda}%")
                  ->orWhere('cliente_telefono', 'like', "%{$busqueda}%")
                  ->orWhereHas('mesa', function ($q2) use ($busqueda) {
                      $q2->where('nombre', 'like', "%{$busqueda}%")
                         ->orWhere('numero', 'like', "%{$busqueda}%");
                  });
            });
        }

        if ($estado = request('estado')) {
            $query->where('estado', $estado);
        }

        $reservaciones = $query->orderBy('fecha_hora')->paginate(20);
        $mesas = Mesa::deSucursal()->orderBy('numero')->get();
        return view('restaurante.reservaciones', compact('reservaciones', 'mesas'));
    }

    public function reservacionesStore(Request $request)
    {
        $data = $request->validate([
            'mesa_id'          => 'required|exists:mesas,id',
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'personas'         => 'required|integer|min:1',
            'fecha_hora'       => 'required|date',
            'notas'            => 'nullable|string|max:500',
        ]);

        $mesa = Mesa::findOrFail($data['mesa_id']);
        if ($mesa->estado !== 'disponible') {
            return back()->with('error', 'La mesa seleccionada no está disponible.');
        }

        $data['user_id'] = Auth::id();

        DB::beginTransaction();
        try {
            Reservacion::create($data);
            $mesa->update(['estado' => 'reservada']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la reservación: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación creada.');
    }

    public function reservacionesUpdate(Request $request, Reservacion $reservacion)
    {
        $data = $request->validate([
            'mesa_id'          => 'required|exists:mesas,id',
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email'    => 'nullable|email|max:200',
            'personas'         => 'required|integer|min:1',
            'fecha_hora'       => 'required|date',
            'notas'            => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $mesaAnterior = $reservacion->mesa;
            $nuevaMesa = Mesa::findOrFail($data['mesa_id']);

            if ($nuevaMesa->id !== $mesaAnterior->id && $nuevaMesa->estado !== 'disponible') {
                return back()->with('error', 'La mesa seleccionada no está disponible.');
            }

            $reservacion->update($data);

            // Liberar mesa anterior si no tiene más reservas activas
            if ($nuevaMesa->id !== $mesaAnterior->id) {
                $otrasReservas = Reservacion::where('mesa_id', $mesaAnterior->id)
                    ->where('id', '!=', $reservacion->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->exists();
                if (!$otrasReservas && $mesaAnterior->estado === 'reservada') {
                    $mesaAnterior->update(['estado' => 'disponible']);
                }
                $nuevaMesa->update(['estado' => 'reservada']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la reservación: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación actualizada.');
    }

    public function reservacionesEstado(Request $request, Reservacion $reservacion)
    {
        $request->validate(['estado' => 'required|in:pendiente,confirmada,cancelada,cumplida']);
        $nuevoEstado = $request->estado;

        DB::beginTransaction();
        try {
            $reservacion->update(['estado' => $nuevoEstado]);

            $mesa = $reservacion->mesa;
            // Si se cancela, liberar la mesa si no tiene otras reservas activas
            if (in_array($nuevoEstado, ['cancelada', 'cumplida'])) {
                $otrasReservas = Reservacion::where('mesa_id', $mesa->id)
                    ->where('id', '!=', $reservacion->id)
                    ->whereIn('estado', ['pendiente', 'confirmada'])
                    ->exists();
                // Solo cambiar a disponible si no hay orden activa y la mesa sigue reservada
                if (!$otrasReservas && !$mesa->ordenActiva && $mesa->estado === 'reservada') {
                    $mesa->update(['estado' => 'disponible']);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }

        return back()->with('success', 'Estado actualizado.');
    }

    public function reservacionesDestroy(Reservacion $reservacion)
    {
        DB::beginTransaction();
        try {
            $mesa = $reservacion->mesa;
            $reservacion->delete();

            // Liberar mesa si no tiene más reservas activas ni orden activa
            $otrasReservas = Reservacion::where('mesa_id', $mesa->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->exists();
            if (!$otrasReservas && !$mesa->ordenActiva && $mesa->estado === 'reservada') {
                $mesa->update(['estado' => 'disponible']);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('restaurante.reservaciones.index')->with('error', 'Error al eliminar: ' . $e->getMessage());
        }

        return redirect()->route('restaurante.reservaciones.index')->with('success', 'Reservación eliminada.');
    }

    // Categorías de mesa
    public function categoriasIndex()
    {
        $categorias = MesaCategoria::orderBy('orden')->orderBy('nombre')->get();
        return view('restaurante.categorias', compact('categorias'));
    }

    public function categoriasShow(MesaCategoria $categoria)
    {
        return response()->json($categoria);
    }

    public function categoriasStore(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'color'  => 'required|string|max:7',
            'icono'  => 'nullable|string|max:50',
            'orden'  => 'nullable|integer|min:0',
        ]);

        MesaCategoria::create($data);

        return redirect()->route('restaurante.categorias.index')->with('success', 'Categoría creada.');
    }

    public function categoriasUpdate(Request $request, MesaCategoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'color'  => 'required|string|max:7',
            'icono'  => 'nullable|string|max:50',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $categoria->update($data);

        return redirect()->route('restaurante.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function categoriasDestroy(MesaCategoria $categoria)
    {
        Mesa::where('categoria_id', $categoria->id)->update(['categoria_id' => null]);
        $categoria->delete();
        return redirect()->route('restaurante.categorias.index')->with('success', 'Categor├¡a eliminada.');
    }

    // Waitlist
    public function waitlistIndex()
    {
        $entries = WaitlistEntry::with('user')
            ->deSucursal()
            ->orderByRaw("FIELD(estado, 'esperando', 'llamando', 'sentado', 'cancelado')")
            ->orderBy('created_at')
            ->get();
        return response()->json(['entries' => $entries]);
    }

    public function waitlistStore(Request $request)
    {
        $data = $request->validate([
            'cliente_nombre'   => 'required|string|max:200',
            'cliente_telefono' => 'nullable|string|max:30',
            'personas'         => 'required|integer|min:1',
            'notas'            => 'nullable|string|max:500',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['user_id'] = Auth::id();
        $data['estado'] = 'esperando';

        $entry = WaitlistEntry::create($data);
        return response()->json(['success' => true, 'entry' => $entry]);
    }

    public function waitlistUpdateEstado(Request $request, WaitlistEntry $entry)
    {
        $estado = $request->validate(['estado' => 'required|in:esperando,llamando,sentado,cancelado']);
        $entry->update($estado);
        return response()->json(['success' => true]);
    }

    public function waitlistDestroy(WaitlistEntry $entry)
    {
        $entry->delete();
        return response()->json(['success' => true]);
    }

    // KDS — Kitchen Display System
    public function kdsIndex()
    {
        return view('restaurante.kds');
    }

    public function kdsOrders()
    {
        $ordenes = Venta::whereIn('estado', ['abierta', 'completada'])
            ->whereHas('detalles', fn($q) => $q->where('estado_cocina', '!=', 'servido'))
            ->with(['mesa:id,numero,nombre', 'detalles' => fn($q) => $q->where('estado_cocina', '!=', 'servido')->with('producto:id,nombre')])
            ->orderBy('created_at')
            ->get()
            ->map(function ($v) {
                $cursos = $v->detalles->groupBy('curso');
                return [
                    'id'       => $v->id,
                    'mesa'     => $v->mesa?->nombre ?? 'Mesa ' . ($v->mesa?->numero ?? '—'),
                    'mesa_id'  => $v->mesa_id,
                    'estado'   => $v->estado,
                    'time'     => $v->created_at->diffForHumans(),
                    'cursos'   => $cursos->toArray(),
                ];
            });

        return response()->json(['ordenes' => $ordenes]);
    }

    public function kdsUpdateEstado(Request $request, VentaDetalle $detalle)
    {
        $request->validate(['estado' => 'required|in:pendiente,preparando,listo,servido']);
        $detalle->update([
            'estado_cocina'     => $request->estado,
            'cocina_updated_at' => now(),
        ]);
        return response()->json(['success' => true]);
    }

    public function kdsAudio()
    {
        $nuevos = VentaDetalle::where('estado_cocina', 'pendiente')
            ->where('cocina_updated_at', '>=', now()->subMinutes(5))
            ->whereDoesntHave('venta', fn($q) => $q->whereIn('estado', ['anulada']))
            ->count();
        return response()->json(['nuevos' => $nuevos]);
    }

    // Mapa de mesas — guardar posición
    public function savePosicion(Request $request, Mesa $mesa)
    {
        $data = $request->validate(['pos_x' => 'required|integer|min:0', 'pos_y' => 'required|integer|min:0']);
        $mesa->update($data);
        return response()->json(['success' => true]);
    }

    public function saveAllPosiciones(Request $request)
    {
        $mesas = $request->validate(['mesas' => 'required|array', 'mesas.*.id' => 'required|exists:mesas,id', 'mesas.*.pos_x' => 'required|integer', 'mesas.*.pos_y' => 'required|integer']);
        foreach ($mesas['mesas'] as $m) {
            Mesa::where('id', $m['id'])->update(['pos_x' => $m['pos_x'], 'pos_y' => $m['pos_y']]);
        }
        return response()->json(['success' => true]);
    }
}
