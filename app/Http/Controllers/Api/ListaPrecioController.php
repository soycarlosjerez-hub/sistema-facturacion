<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListaPrecioResource;
use App\Models\ListaPrecio;
use Illuminate\Http\Request;

class ListaPrecioController extends Controller
{
    public function index(Request $request)
    {
        $query = ListaPrecio::with(['sucursal', 'productos'])
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->activa, fn ($q) => $q->where('activa', true));

        return ListaPrecioResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'required|numeric|min:0',
            'sucursal_id' => 'required|exists:sucursales,id',
            'activa' => 'boolean',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $lista = ListaPrecio::create($validated);

        return new ListaPrecioResource($lista->load(['sucursal', 'productos']));
    }

    public function show(ListaPrecio $listaPrecio)
    {
        return new ListaPrecioResource($listaPrecio->load(['sucursal', 'productos']));
    }

    public function update(Request $request, ListaPrecio $listaPrecio)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'nullable|string',
            'porcentaje' => 'sometimes|numeric|min:0',
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'activa' => 'boolean',
        ]);

        $listaPrecio->update($validated);

        return new ListaPrecioResource($listaPrecio->load(['sucursal', 'productos']));
    }

    public function destroy(ListaPrecio $listaPrecio)
    {
        $listaPrecio->delete();
        return response()->json(['message' => 'Lista de precios eliminada.']);
    }
}
