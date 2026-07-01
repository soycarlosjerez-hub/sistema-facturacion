<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservacionResource;
use App\Models\Reservacion;
use Illuminate\Http\Request;

class ReservacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservacion::with(['cliente', 'mesa', 'sucursal'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->mesa_id, fn ($q) => $q->where('mesa_id', $request->mesa_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->fecha, fn ($q) => $q->whereDate('fecha_hora', $request->fecha));

        return ReservacionResource::collection($query->orderBy('fecha_hora', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'mesa_id' => 'required|exists:mesas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'fecha_hora' => 'required|date',
            'personas' => 'required|integer|min:1',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $reservacion = Reservacion::create($validated);

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'sucursal']));
    }

    public function show(Reservacion $reservacion)
    {
        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'sucursal']));
    }

    public function update(Request $request, Reservacion $reservacion)
    {
        $validated = $request->validate([
            'cliente_id' => 'sometimes|exists:clientes,id',
            'mesa_id' => 'sometimes|exists:mesas,id',
            'fecha_hora' => 'sometimes|date',
            'personas' => 'sometimes|integer|min:1',
            'estado' => 'sometimes|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $reservacion->update($validated);

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'sucursal']));
    }

    public function destroy(Reservacion $reservacion)
    {
        $reservacion->delete();
        return response()->json(['message' => 'Reservación eliminada.']);
    }
}
