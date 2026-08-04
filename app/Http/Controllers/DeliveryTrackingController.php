<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryTrackingController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryTracking::with(['orden', 'driver'])->query();

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

        return view('delivery-tracking.show', compact('tracking'));
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
}
