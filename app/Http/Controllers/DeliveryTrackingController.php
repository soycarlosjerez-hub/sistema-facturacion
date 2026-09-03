<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTracking;
use App\Models\DeliveryDriver;
use App\Services\DriverAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryTracking::with(['orden', 'driver']);

        if ($orderId = $request->input('order_id')) {
            $query->where('orden_id', $orderId);
        }

        if ($driverId = $request->input('driver_id')) {
            $query->where('driver_id', $driverId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $trackings = $query->latest()->paginate(20)->withQueryString();

        return view('delivery-tracking.index', compact('trackings'));
    }

    public function show($id)
    {
        $tracking = DeliveryTracking::with(['orden.driver', 'orden.cliente', 'driver', 'creador'])->findOrFail($id);

        // Construir línea de tiempo de eventos basada en el estado del tracking
        $events = [
            (object)[
                'descripcion'   => 'Seguimiento creado',
                'created_at'    => $tracking->created_at,
                'completed'     => true,
                'is_current'    => false,
                'nota'          => null,
                'usuario'       => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
            ],
            (object)[
                'descripcion'   => 'En camino',
                'created_at'    => $tracking->updated_at,
                'completed'     => false,
                'is_current'    => $tracking->status === 'en_camino',
                'nota'          => $tracking->notas ?: null,
                'usuario'       => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
            ],
        ];

        switch ($tracking->status) {
            case 'entregado':
                $events[] = (object)[
                    'descripcion'   => 'Entrega confirmada',
                    'created_at'    => $tracking->updated_at,
                    'completed'     => true,
                    'is_current'    => true,
                    'nota'          => $tracking->notas ?: null,
                    'usuario'       => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
                ];
                break;

            case 'fallido':
                $events[] = (object)[
                    'descripcion'   => 'Entrega fallida',
                    'created_at'    => $tracking->updated_at,
                    'completed'     => true,
                    'is_current'    => true,
                    'nota'          => $tracking->notas ?: null,
                    'usuario'       => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
                ];
                break;

            case 'cancelado':
                $events[] = (object)[
                    'descripcion'   => 'Seguimiento cancelado',
                    'created_at'    => $tracking->updated_at,
                    'completed'     => true,
                    'is_current'    => true,
                    'nota'          => $tracking->notas ?: null,
                    'usuario'       => $tracking->creador->name ?? $tracking->creador->email ?? 'Sistema',
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
            if (!empty($data['foto_evidencia'])) {
                $ordenData['prueba_entrega_foto'] = $data['foto_evidencia'];
            }
            if (!empty($data['firma_cliente'])) {
                $ordenData['prueba_entrega_firma'] = $data['firma_cliente'];
            }
            if (!empty($data['notas'])) {
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
     */
    public function driversQueue()
    {
        $drivers = DeliveryDriver::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $drivers->each(function ($driver) {
            $activeTrackings = DeliveryTracking::where('driver_id', $driver->id)
                ->whereIn('status', ['creado', 'en_camino'])
                ->with(['orden.cliente', 'orden.detalles'])
                ->latest()
                ->get();

            $driver->pendientes = $activeTrackings->where('status', 'creado');
            $driver->enCamino = $activeTrackings->where('status', 'en_camino');
            $driver->totalActivas = $activeTrackings->count();

            $todayStart = \Carbon\Carbon::today()->startOfDay();
            $todayEnd = \Carbon\Carbon::today()->endOfDay();

            $driver->entregadasHoy = DeliveryTracking::where('driver_id', $driver->id)
                ->where('status', 'entregado')
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->count();
        });

        return view('delivery-drivers.queue', compact('drivers'));
    }
}
