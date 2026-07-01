<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DevolucionResource;
use App\Models\Devolucion;
use Illuminate\Http\Request;

class DevolucionController extends Controller
{
    public function index(Request $request)
    {
        $query = Devolucion::with(['venta', 'cliente', 'sucursal', 'user', 'detalles'])
            ->when($request->venta_id, fn ($q) => $q->where('venta_id', $request->venta_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado));

        return DevolucionResource::collection($query->orderBy('fecha', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venta_id' => 'required|exists:ventas,id',
            'cliente_id' => 'nullable|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'user_id' => 'required|exists:users,id',
            'ncf' => 'required|string|max:50',
            'fecha' => 'nullable|date',
            'estado' => 'required|string|max:20',
            'motivo' => 'nullable|string',
            'total_devuelto' => 'required|numeric|min:0',
            'detalles' => 'required|array',
            'detalles.*.venta_detalle_id' => 'required|exists:venta_detalles,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        $devolucion = Devolucion::create($validated);

        foreach ($validated['detalles'] as $detalle) {
            $devolucion->detalles()->create(array_merge($detalle, ['devolucion_id' => $devolucion->id]));
        }

        return new DevolucionResource($devolucion->load(['venta', 'cliente', 'sucursal', 'user', 'detalles']));
    }

    public function show(Devolucion $devolucion)
    {
        return new DevolucionResource($devolucion->load(['venta', 'cliente', 'sucursal', 'user', 'detalles']));
    }

    public function update(Request $request, Devolucion $devolucion)
    {
        $validated = $request->validate([
            'estado' => 'sometimes|string|max:20',
            'motivo' => 'nullable|string',
            'total_devuelto' => 'sometimes|numeric|min:0',
        ]);

        $devolucion->update($validated);

        return new DevolucionResource($devolucion->load(['venta', 'cliente', 'sucursal', 'user', 'detalles']));
    }

    public function destroy(Devolucion $devolucion)
    {
        $devolucion->delete();
        return response()->json(['message' => 'Devolución eliminada.']);
    }
}
