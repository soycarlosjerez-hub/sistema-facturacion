<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlmacenResource;
use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $query = Almacen::with(['sucursal'])
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->search, fn ($q) => $q->where('nombre', 'like', '%' . $request->search . '%'));

        return AlmacenResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'sucursal_id' => 'required|exists:sucursales,id',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $almacen = Almacen::create($validated);

        return new AlmacenResource($almacen->load('sucursal'));
    }

    public function show(Almacen $almacen)
    {
        return new AlmacenResource($almacen->load('sucursal'));
    }

    public function update(Request $request, Almacen $almacen)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'ubicacion' => 'nullable|string|max:500',
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $almacen->update($validated);

        return new AlmacenResource($almacen->load('sucursal'));
    }

    public function destroy(Almacen $almacen)
    {
        $almacen->delete();
        return response()->json(['message' => 'Almacén eliminado.']);
    }
}
