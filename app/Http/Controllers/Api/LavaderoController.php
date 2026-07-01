<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LavaderoResource;
use App\Models\Lavadero;
use Illuminate\Http\Request;

class LavaderoController extends Controller
{
    public function index(Request $request)
    {
        $query = Lavadero::with(['cliente', 'sucursal', 'user', 'vehiculo'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado));

        return LavaderoResource::collection($query->orderBy('fecha_ingreso', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50',
            'cliente_id' => 'required|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'user_id' => 'required|exists:users,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_ingreso' => 'nullable|date',
            'fecha_entrega' => 'nullable|date',
            'estado' => 'required|string|max:20',
            'servicio' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $lavadero = Lavadero::create($validated);

        return new LavaderoResource($lavadero->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function show(Lavadero $lavadero)
    {
        return new LavaderoResource($lavadero->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function update(Request $request, Lavadero $lavadero)
    {
        $validated = $request->validate([
            'estado' => 'sometimes|string|max:20',
            'fecha_entrega' => 'sometimes|date',
            'total' => 'sometimes|numeric|min:0',
            'servicio' => 'sometimes|string|max:255',
            'notas' => 'nullable|string',
        ]);

        $lavadero->update($validated);

        return new LavaderoResource($lavadero->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function destroy(Lavadero $lavadero)
    {
        $lavadero->delete();
        return response()->json(['message' => 'Registro de lavandería eliminado.']);
    }
}
