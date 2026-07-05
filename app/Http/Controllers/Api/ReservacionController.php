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
        $query = Reservacion::with(['cliente', 'mesa'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->mesa_id, fn ($q) => $q->where('mesa_id', $request->mesa_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado))
            ->when($request->fecha, fn ($q) => $q->whereDate('fecha_hora', $request->fecha));

        return ReservacionResource::collection($query->orderBy('fecha_hora', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:200',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'mesa_id' => 'required|exists:mesas,id',
            'fecha_hora' => 'required|date',
            'personas' => 'required|integer|min:1',
            'estado' => 'required|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['tenant_id'] = auth()->user()->business_instance_id ?? null;
        $validated['sucursal_id'] = \App\Models\Mesa::find($validated['mesa_id'])->sucursal_id ?? null;

        $reservacion = Reservacion::create($validated);

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function show(Reservacion $reservacion)
    {
        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function update(Request $request, Reservacion $reservacion)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'sometimes|string|max:200',
            'cliente_id' => 'nullable|exists:clientes,id',
            'cliente_telefono' => 'nullable|string|max:30',
            'cliente_email' => 'nullable|email|max:200',
            'mesa_id' => 'sometimes|exists:mesas,id',
            'fecha_hora' => 'sometimes|date',
            'personas' => 'sometimes|integer|min:1',
            'estado' => 'sometimes|string|max:20',
            'notas' => 'nullable|string',
        ]);

        $reservacion->update($validated);

        return new ReservacionResource($reservacion->load(['cliente', 'mesa', 'user']));
    }

    public function destroy(Reservacion $reservacion)
    {
        $reservacion->delete();
        return response()->json(['message' => 'Reservación eliminada.']);
    }
}
