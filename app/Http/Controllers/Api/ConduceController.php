<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConduceResource;
use App\Models\Conduce;
use Illuminate\Http\Request;

class ConduceController extends Controller
{
    public function index(Request $request)
    {
        $query = Conduce::with(['cliente', 'sucursal', 'user', 'vehiculo'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado));

        return ConduceResource::collection($query->orderBy('fecha_entrega', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50',
            'cliente_id' => 'required|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'user_id' => 'required|exists:users,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_recepcion' => 'nullable|date',
            'fecha_entrega' => 'nullable|date',
            'estado' => 'required|string|max:20',
            'total' => 'required|numeric|min:0',
            'kilometraje' => 'nullable|integer',
            'combustible' => 'nullable|string|max:50',
            'danios' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $conduce = Conduce::create($validated);

        return new ConduceResource($conduce->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function show(Conduce $conduce)
    {
        return new ConduceResource($conduce->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function update(Request $request, Conduce $conduce)
    {
        $validated = $request->validate([
            'estado' => 'sometimes|string|max:20',
            'fecha_entrega' => 'sometimes|date',
            'total' => 'sometimes|numeric|min:0',
            'kilometraje' => 'sometimes|integer',
            'combustible' => 'nullable|string|max:50',
            'danios' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $conduce->update($validated);

        return new ConduceResource($conduce->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function destroy(Conduce $conduce)
    {
        $conduce->delete();
        return response()->json(['message' => 'Registro de entrega eliminado.']);
    }
}
