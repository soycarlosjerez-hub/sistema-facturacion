<?php

namespace App\Http\Controllers;

use App\Models\DeliveryCompany;
use App\Models\DeliveryDriver;
use App\Models\DeliveryZone;
use App\Services\DeliveryService;
use App\Services\DriverAssignmentService;
use App\Services\PosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    protected PosService $service;

    public function __construct(PosService $service)
    {
        $this->service = $service;
    }

    /**
     * Vista principal del POS unificado (lavadero + tienda)
     */
    public function index()
    {
        $servicios = \App\Models\LavaderoServicio::activos()->orderBy('orden')->get();
        $paquetes = \App\Models\LavaderoPaquete::activos()->orderBy('orden')->get();
        $productos = \App\Models\Producto::activos()->orderBy('nombre')->get();
        $categorias = \App\Models\Categoria::activas()->orderBy('nombre')->get();
        $lavadores = \App\Models\Lavador::activos()->orderBy('nombre')->get();
        $clientes = \App\Models\Cliente::orderBy('nombre')->limit(50)->get();

        // Cálculo de ITBIS desde la instancia
        $itbisPorcentaje = \App\Models\SystemSetting::itbisDefault();

        return view('pos.index', compact(
            'servicios', 'paquetes', 'productos', 'categorias',
            'lavadores', 'clientes', 'itbisPorcentaje'
        ));
    }

    /**
     * Procesar la venta mixta (servicio + productos + paquete)
     */
    public function checkout(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente_id'   => 'nullable|exists:clientes,id',
                'vehiculo_id'  => 'nullable|exists:vehiculos,id',
                'metodo_pago'  => 'required|string|in:efectivo,tarjeta,transferencia,fiado',
                'tipo_venta_id'=> 'nullable|exists:tipos_ventas,id',
                'servicios'    => 'nullable|array',
                'productos'    => 'nullable|array',
                'paquetes'     => 'nullable|array',
            ]);

            $result = $this->service->checkout($data);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Búsqueda rápida de productos para el POS
     */
    public function quickSale(Request $request)
    {
        $query = $request->input('search', '');
        $linea = $request->input('linea');

        $productos = $this->service->quickSearch($query, $linea);

        return response()->json([
            'productos' => $productos->map(function ($p) {
                return [
                    'id'           => (int) $p->id,
                    'nombre'       => $p->nombre,
                    'codigo_barras'=> $p->codigo_barras,
                    'precio'       => (float) $p->precio,
                    'stock'        => (int) $p->stock,
                    'imagen'       => $p->imagen,
                    'categoria_id' => (int) $p->categoria_id,
                ];
            }),
        ]);
    }

    /**
     * Guardar venta en espera (hold)
     */
    public function hold(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente_id'  => 'nullable|exists:clientes,id',
                'vehiculo_id' => 'nullable|exists:vehiculos,id',
                'servicios'   => 'nullable|array',
                'productos'   => 'nullable|array',
                'paquetes'    => 'nullable|array',
                'metodo_pago' => 'nullable|string|in:efectivo,tarjeta,transferencia',
                'total'       => 'nullable|numeric|min:0',
            ]);

            $result = $this->service->holdSale($data);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Restaurar venta en espera
     */
    public function restore(Request $request)
    {
        try {
            $holdId = $request->input('hold_id');

            if (!$holdId) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Se requiere hold_id',
                ], 422);
            }

            $data = $this->service->restoreSale($holdId);

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Calcular total de items actuales
     */
    public function calcularTotal(Request $request)
    {
        $items = $request->input('items', []);

        $result = $this->service->calculateTotal($items);

        return response()->json($result);
    }

    /**
     * Listar ventas en espera del usuario actual
     */
    public function holdsList()
    {
        $userId = auth()->id();
        $prefix = 'hold_' . $userId . '_';
        $holds = [];

        foreach (session()->all() as $key => $value) {
            if (str_starts_with($key, $prefix)) {
                $holdId = str_replace($prefix, '', $key);
                $holds[] = [
                    'hold_id'   => $holdId,
                    'data'      => $value,
                ];
            }
        }

        return response()->json(['holds' => array_values($holds)]);
    }

    /**
     * Obtener zonas de delivery activas
     */
    public function getDeliveryZones()
    {
        $zones = DeliveryZone::where('activo', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'descripcion', 'tiempo_estimado_minutos', 'radio_km']);

        return response()->json(['zones' => $zones]);
    }

    /**
     * Obtener empresas de delivery activas
     */
    public function getDeliveryCompanies()
    {
        $companies = DeliveryCompany::where('activo', true)
            ->orderBy('nombre_corto')
            ->get(['id', 'nombre', 'nombre_corto', 'comision_porcentaje']);

        return response()->json(['companies' => $companies]);
    }

    /**
     * Checkout con delivery — crea venta + asigna driver + tracking
     */
    public function checkoutDelivery(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'carrito'           => 'required|array',
            'carrito.*.id'      => 'required',
            'carrito.*.cantidad'=> 'required|integer|min:1',
            'carrito.*.precio'  => 'required|numeric|min:0',
            'metodo_pago'       => 'required|string|in:efectivo,tarjeta,transferencia,fiado',
            'direccion_entrega' => 'required_if:tipoVenta,delivery|string|max:500',
            'telefono_contacto' => 'required_if:tipoVenta,delivery|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validación fallida'], 422);
        }

        try {
            $tenantId = auth()->user()->business_instance_id;
            $tipoComprobante = \App\Models\SystemSetting::getValue('tipo_comprobante_pos', 'sin');

            $carrito = $request->input('carrito');
            $metodoPago = $request->input('metodo_pago');
            $notas = $request->input('notas', '');
            $propina = (float) ($request->input('propina') ?? 0);
            $deliveryFee = (float) ($request->input('delivery_fee') ?? 0);
            $empresaId = $request->input('entrega_empresa_id');
            $zonaId = $request->input('delivery_zone_id');
            $direccion = $request->input('direccion_entrega');
            $telefono = $request->input('telefono_contacto');
            $clienteId = $request->input('cliente_id');

            // Buscar producto/servicio por ID (soporta ambos)
            $lineItems = [];
            $subtotal = 0;

            foreach ($carrito as $item) {
                $producto = \App\Models\Producto::find($item['id']);
                $servicio = \App\Models\LavaderoServicio::find($item['id']);
                $paquete = \App\Models\LavaderoPaquete::find($item['id']);

                $tipo = 'producto';
                $linea = null;

                if ($servicio) {
                    $tipo = 'servicio';
                    $linea = $servicio;
                } elseif ($paquete) {
                    $tipo = 'paquete';
                    $linea = $paquete;
                } else {
                    $linea = $producto;
                }

                $cantidad = (int) $item['cantidad'];
                $precio = (float) $item['precio'];
                $totalLinea = $cantidad * $precio;
                $subtotal += $totalLinea;

                $lineItems[] = [
                    'tipo'      => $tipo,
                    'id'        => $item['id'],
                    'cantidad'  => $cantidad,
                    'precio'    => $precio,
                    'subtotal'  => $totalLinea,
                    'linea'     => $linea,
                ];
            }

            $total = $subtotal + $deliveryFee;
            $estado = match ($metodoPago) {
                'fiado' => 'pendiente',
                default => 'completada',
            };

            $db = DB::connection();
            $db->beginTransaction();

            try {
                // Verificar caja activa
                $isElevated = in_array(auth()->user()->role, ['admin', 'owner', 'admin-business', 'root'])
                    || auth()->user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);
                $sesionQuery = \App\Models\SesionCaja::where('estado', 'abierta');
                if (!$isElevated) {
                    $sesionQuery->where('user_id', auth()->id());
                }
                $sesionCaja = $sesionQuery->first();

                if (!$sesionCaja) {
                    return response()->json([
                        'success' => false,
                        'error' => 'No hay una sesión de caja activa. Por favor abra la caja primero.',
                    ], 422);
                }

                // Consumidor final si no hay cliente
                if (!$clienteId) {
                    $consumidorFinal = \App\Models\Cliente::consumidorFinal($tenantId);
                    $clienteId = $consumidorFinal->id;
                }

                // Calcular ITBIS
                $itbisPct = \App\Models\SystemSetting::itbisDefault();
                $impuestos = round($subtotal * ($itbisPct / 100), 2);

                // Calcular cargo de servicio si aplica
                $cargoServicioPct = (float) \App\Models\SystemSetting::getValue('cargo_servicio_porcentaje', '0');
                $cargoServicio = 0;
                if ($cargoServicioPct > 0) {
                    $cargoServicio = round($subtotal * ($cargoServicioPct / 100), 2);
                }

                // Crear la venta con campos de delivery
                $venta = \App\Models\Venta::create([
                    'ncf'              => '',
                    'ncf_tipo'         => 'sin',
                    'tipo_comprobante' => $tipoComprobante,
                    'user_id'          => auth()->id(),
                    'sucursal_id'      => session('sucursal_id'),
                    'caja_id'          => $sesionCaja->caja_id,
                    'sesion_caja_id'   => $sesionCaja->id,
                    'cliente_id'       => $clienteId,
                    'tipo_orden'       => 'delivery',
                    'fecha'            => now(),
                    'subtotal'         => $subtotal,
                    'impuestos'        => $impuestos,
                    'total'            => round($total + $impuestos + $cargoServicio, 2),
                    'estado'           => $estado,
                    'propina'          => $propina,
                    'delivery_fee'     => $deliveryFee,
                    'cargo_servicio'   => $cargoServicio,
                    'delivery_company_id' => $empresaId,
                    'delivery_address' => $direccion,
                    'tenant_id'        => $tenantId,
                ]);

                // Crear detalles de venta
                foreach ($lineItems as $line) {
                    if ($line['tipo'] === 'servicio') {
                        \App\Models\VentaDetalle::create([
                            'venta_id'         => $venta->id,
                            'servicio_id'      => $line['id'],
                            'cantidad'         => $line['cantidad'],
                            'precio_unitario'  => $line['precio'],
                            'subtotal'         => $line['subtotal'],
                            'tenant_id'        => $tenantId,
                            'tipo_linea'       => 'servicio',
                        ]);
                    } elseif ($line['tipo'] === 'paquete') {
                        \App\Models\VentaDetalle::create([
                            'venta_id'         => $venta->id,
                            'cantidad'         => $line['cantidad'],
                            'precio_unitario'  => $line['precio'],
                            'subtotal'         => $line['subtotal'],
                            'tenant_id'        => $tenantId,
                            'tipo_linea'       => 'paquete',
                        ]);
                    } else {
                        \App\Models\VentaDetalle::create([
                            'venta_id'         => $venta->id,
                            'producto_id'      => $line['id'],
                            'cantidad'         => $line['cantidad'],
                            'precio_unitario'  => $line['precio'],
                            'subtotal'         => $line['subtotal'],
                            'tenant_id'        => $tenantId,
                            'tipo_linea'       => 'producto',
                        ]);

                        // Reducir stock
                        $producto = \App\Models\Producto::find($line['id']);
                        if ($producto && $producto->tiene_almacen) {
                            $producto->decrement('stock', $line['cantidad']);
                        }
                    }
                }

                // Procesar pago
                if ($estado !== 'pendiente') {
                    $montoPago = round($total + $impuestos + $cargoServicio, 2);
                    \App\Models\Pago::create([
                        'venta_id'     => $venta->id,
                        'monto'        => $montoPago,
                        'metodo_pago'  => $metodoPago,
                        'tenant_id'    => $tenantId,
                    ]);

                    // Actualizar caja
                    $cobrosColumn = 'cobros_' . $metodoPago;
                    \App\Models\Caja::where('id', $sesionCaja->caja_id)
                        ->increment($cobrosColumn, $montoPago);
                }

                // Calcular retenciones
                $cliente = $venta->cliente;
                if ($cliente) {
                    $retentionService = app(\App\Services\RetentionService::class);
                    $retenciones = $retentionService->calcularRetencionesVenta($venta, $cliente);
                    $retentionService->guardarRetencionesVenta($venta, $retenciones);
                }

                // Si es delivery, asignar driver y crear tracking
                if ($request->input('tipoVenta') === 'delivery' && $direccion) {
                    $driverService = app(DriverAssignmentService::class);
                    $driverResult = $driverService->asignarDriverAVenta($venta->id, null, $zonaId);

                    if ($driverResult['success'] ?? false) {
                        // Actualizar la venta con el driver asignado
                        $venta->driver_id = $driverResult['driver']['id'];
                        $venta->save();
                    }
                }

                // Evento
                \Illuminate\Support\Facades\Event::dispatch(new \App\Events\SaleCreated($venta));

                $db->commit();

                // Cargar relaciones para respuesta
                $venta->load(['cliente', 'detalles.producto', 'driver', 'deliveryCompany']);

                return response()->json([
                    'success' => true,
                    'message' => $request->input('tipoVenta') === 'delivery'
                        ? 'Venta con delivery creada y driver asignado'
                        : 'Venta creada exitosamente',
                    'venta_id' => $venta->id,
                    'venta' => $venta,
                    'redirect' => route('ventas.show', $venta->id),
                ]);

            } catch (\Exception $e) {
                $db->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('POS Checkout Delivery Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar la venta: ' . $e->getMessage(),
            ], 500);
        }
    }
}
