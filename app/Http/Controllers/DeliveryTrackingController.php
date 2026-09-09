<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use App\Models\DeliveryTracking;
use App\Models\Venta;
use App\Services\DriverAssignmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::where('tipo_orden', 'delivery')
            ->with(['cliente', 'driver', 'deliveryZone', 'deliveryTracking', 'orden']);

        // Si es driver, solo ver sus propias entregas
        if (Auth::user()->hasRole('delivery') && Auth::user()->deliveryDriver) {
            $query->where('driver_id', Auth::user()->deliveryDriver->id);
        }

        if ($orderId = $request->input('order_id')) {
            $query->where('id', $orderId);
        }

        if ($driverId = $request->input('driver_id')) {
            $query->where('driver_id', $driverId);
        }

        if ($status = $request->input('status')) {
            $query->where(function ($q) use ($status) {
                $q->whereHas('deliveryTracking', function ($sub) use ($status) {
                    $sub->where('status', $status);
                });
                if ($status === 'creado') {
                    $q->orWhere(function ($sub) {
                        $sub->whereIn('estado', ['pendiente', 'abierta'])
                            ->whereDoesntHave('deliveryTracking');
                    });
                } elseif ($status === 'en_camino') {
                    $q->orWhere(function ($sub) {
                        $sub->whereNotNull('driver_id')
                            ->whereNotIn('estado', ['cobrada', 'cancelada', 'pendiente', 'abierta'])
                            ->whereDoesntHave('deliveryTracking');
                    });
                } elseif ($status === 'entregado') {
                    $q->orWhere('estado', 'cobrada');
                }
            });
        }

        $ventas = $query->latest()->paginate(20)->withQueryString();

        $trackings = $ventas->through(function ($venta) {
            $venta->_virtual_status = $this->resolveStatus($venta);

            return $venta;
        });

        $orders = Venta::where('tipo_orden', 'delivery')->orderBy('id', 'desc')->get(['id']);
        $drivers = DeliveryDriver::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido']);

        return view('delivery-tracking.index', compact('trackings', 'orders', 'drivers'));
    }

    private function resolveStatus(Venta $venta): string
    {
        if ($venta->deliveryTracking) {
            return $venta->deliveryTracking->status;
        }

        if (in_array($venta->estado, ['pendiente', 'abierta'])) {
            return 'creado';
        }

        if ($venta->estado === 'cobrada') {
            return 'entregado';
        }

        if ($venta->driver_id && ! in_array($venta->estado, ['cancelada'])) {
            return 'en_camino';
        }

        return 'creado';
    }

    public function show($id)
    {
        $tracking = DeliveryTracking::with(['orden.driver', 'orden.cliente', 'driver', 'creador'])->findOrFail($id);

        // Construir línea de tiempo de eventos basada en el estado del tracking
        $events = [
            (object) [
                'descripcion' => 'Seguimiento creado',
                'created_at' => $tracking->created_at,
                'completed' => true,
                'is_current' => false,
                'nota' => null,
                'usuario' => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
            ],
            (object) [
                'descripcion' => 'En camino',
                'created_at' => $tracking->updated_at,
                'completed' => false,
                'is_current' => $tracking->status === 'en_camino',
                'nota' => $tracking->notas ?: null,
                'usuario' => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
            ],
        ];

        switch ($tracking->status) {
            case 'entregado':
                $events[] = (object) [
                    'descripcion' => 'Entrega confirmada',
                    'created_at' => $tracking->updated_at,
                    'completed' => true,
                    'is_current' => true,
                    'nota' => $tracking->notas ?: null,
                    'usuario' => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
                ];
                break;

            case 'fallido':
                $events[] = (object) [
                    'descripcion' => 'Entrega fallida',
                    'created_at' => $tracking->updated_at,
                    'completed' => true,
                    'is_current' => true,
                    'nota' => $tracking->notas ?: null,
                    'usuario' => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
                ];
                break;

            case 'cancelado':
                $events[] = (object) [
                    'descripcion' => 'Seguimiento cancelado',
                    'created_at' => $tracking->updated_at,
                    'completed' => true,
                    'is_current' => true,
                    'nota' => $tracking->notas ?: null,
                    'usuario' => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
                ];
                break;
        }

        // Recalcular "completado" del evento "En camino": si el tracking ya tiene estado final, está completado
        if (in_array($tracking->status, ['entregado', 'fallido', 'cancelado'])) {
            $events[1]->completed = true;
        }

        return view('delivery-tracking.show', compact('tracking', 'events'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'orden_id' => 'required|exists:ordenes,id',
            'driver_id' => 'nullable|exists:delivery_drivers,id',
            'status' => 'required|in:creado,en_camino,entregado,fallido,cancelado',
            'notas' => 'nullable|string',
            'latitud' => 'nullable|numeric|min:-90|max:90',
            'longitud' => 'nullable|numeric|min:-180|max:180',
        ]);

        $data['creado_por'] = Auth::id();
        $data['tenant_id'] = Auth::user()->business_instance_id;

        DeliveryTracking::create($data);

        return redirect()->back()->with('success', 'Seguimiento registrado correctamente.');
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:creado,en_camino,entregado,fallido,cancelado',
            'notas' => 'nullable|string',
        ]);

        $tracking = DeliveryTracking::findOrFail($id);
        $tracking->update($data);

        // Actualizar también el estado en la orden relacionada
        if ($tracking->orden) {
            $tracking->orden->update(['tracking_status' => $data['status']]);
        }

        return redirect()->back()->with('success', 'Estado actualizado correctamente.');
    }

    public function registrarUbicacion(Request $request, $id)
    {
        $data = $request->validate([
            'latitud' => 'required|numeric|min:-90|max:90',
            'longitud' => 'required|numeric|min:-180|max:180',
        ]);

        $tracking = DeliveryTracking::findOrFail($id);
        $tracking->update([
            'latitud' => $data['latitud'],
            'longitud' => $data['longitud'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ubicación registrada.',
            'data' => [
                'latitud' => $tracking->latitud,
                'longitud' => $tracking->longitud,
            ],
        ]);
    }

    public function confirmarEntrega(Request $request, $id)
    {
        $data = $request->validate([
            'foto_evidencia' => 'nullable|string|max:500',
            'firma_cliente' => 'nullable|string|max:500',
            'notas' => 'nullable|string',
        ]);

        $tracking = DeliveryTracking::findOrFail($id);
        $tracking->update([
            'status' => DeliveryTracking::STATUS_ENTREGADO,
            'notas' => $data['notas'] ?? null,
        ]);

        // Actualizar orden relacionada
        if ($tracking->orden) {
            $ordenData = ['tracking_status' => DeliveryTracking::STATUS_ENTREGADO];
            if (! empty($data['foto_evidencia'])) {
                $ordenData['prueba_entrega_foto'] = $data['foto_evidencia'];
            }
            if (! empty($data['firma_cliente'])) {
                $ordenData['prueba_entrega_firma'] = $data['firma_cliente'];
            }
            if (! empty($data['notas'])) {
                $ordenData['notas_entrega'] = $data['notas'];
            }
            $tracking->orden->update($ordenData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Entrega confirmada correctamente.',
        ]);
    }

    /**
     * Asigna un delivery driver a una orden
     */
    public function asignarDriver(Request $request, $ordenId)
    {
        $data = $request->validate([
            'driver_id' => 'nullable|integer|exists:delivery_drivers,id',
        ]);

        $result = app(DriverAssignmentService::class)->asignarDriver($ordenId, $data['driver_id'] ?? null);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver asignado correctamente.',
            'data' => [
                'orden' => $result['orden'],
                'driver' => $result['driver'],
            ],
        ]);
    }

    /**
     * Libera la asignación del driver de una orden
     */
    public function liberarDriver($ordenId)
    {
        $result = app(DriverAssignmentService::class)->liberarDriver($ordenId);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], $result['code'] ?? 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver liberado correctamente.',
        ]);
    }

    /**
     * Cola de Drivers — ver todo lo que tiene cada repartidor
     * Si el usuario es driver (role delivery) y tiene deliveryDriver vinculado, solo ve su propia card
     */
    public function driversQueue()
    {
        $query = DeliveryDriver::where('activo', true)
            ->with('user')
            ->orderBy('nombre');

        // Si es driver y tiene deliveryDriver vinculado, solo ver su propia card
        if (Auth::user()->hasRole('delivery') && Auth::user()->deliveryDriver) {
            $query->where('id', Auth::user()->deliveryDriver->id);
        }

        $drivers = $query->get();

        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $drivers->each(function ($driver) use ($todayStart, $todayEnd) {
            $ventasActivas = Venta::where('driver_id', $driver->id)
                ->where('tipo_orden', 'delivery')
                ->whereNotIn('estado', ['cobrada', 'cancelada'])
                ->with(['orden.cliente', 'orden.detalles', 'deliveryTracking'])
                ->latest()
                ->get();

            $driver->pendientes = $ventasActivas->filter(function ($v) {
                return ! $v->deliveryTracking || $v->deliveryTracking->status === 'creado';
            })->values();

            $driver->enCamino = $ventasActivas->filter(function ($v) {
                return $v->deliveryTracking && $v->deliveryTracking->status === 'en_camino';
            })->values();

            $driver->totalActivas = $ventasActivas->count();

            $driver->entregadasHoy = Venta::where('driver_id', $driver->id)
                ->where('tipo_orden', 'delivery')
                ->where('estado', 'cobrada')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
        });

        return view('delivery-drivers.queue', compact('drivers'));
    }

    /**
     * Vista "Mis Entregas" — solo las órdenes asignadas al driver logueado
     */
    public function myDeliveries()
    {
        $userId = Auth::id();

        $ventas = Venta::where('tipo_orden', 'delivery')
            ->where('driver_id', $userId)
            ->with(['cliente', 'deliveryTracking', 'orden'])
            ->get()
            ->map(function ($venta) {
                $venta->_virtual_status = $this->resolveStatus($venta);

                return $venta;
            });

        $pendientes = $ventas->filter(fn ($v) => $v->_virtual_status === 'creado');
        $enCamino = $ventas->filter(fn ($v) => $v->_virtual_status === 'en_camino');
        $entregadasHoy = Venta::where('driver_id', $userId)
            ->where('tipo_orden', 'delivery')
            ->where('estado', 'cobrada')
            ->whereDate('created_at', today())
            ->count();

        return view('delivery-mis-entregas.index', compact('ventas', 'pendientes', 'enCamino', 'entregadasHoy'));
    }

    /**
     * Cambio rápido de estatus desde la vista del driver — solo permite cambios seguros
     */
    public function driverUpdateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|in:en_camino,entregado,fallido',
            'notas' => 'nullable|string|max:500',
        ]);

        $tracking = DeliveryTracking::findOrFail($id);

        // Verificar que el tracking pertenece al driver logueado
        $venta = Venta::where('id', $tracking->venta_id)
            ->where('driver_id', Auth::id())
            ->first();

        if (! $venta) {
            return response()->json([
                'success' => false,
                'message' => 'Esta entrega no está asignada a tu cuenta.',
            ], 403);
        }

        $tracking->update([
            'status' => $data['status'],
            'notas' => $data['notas'] ?? null,
        ]);

        if ($tracking->orden) {
            $tracking->orden->update(['tracking_status' => $data['status']]);
        }

        // Si es "entregado", marcar la venta como completada
        if ($data['status'] === 'entregado') {
            $venta->update(['estado' => 'cobrada']);
        }

        return redirect()->route('delivery-mis-entregas')
            ->with('success', 'Estado actualizado correctamente.');
    }
}
