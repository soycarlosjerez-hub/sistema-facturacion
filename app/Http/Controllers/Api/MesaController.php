<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MesaResource;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mesa::with(['categoria', 'ubicacion', 'sucursal', 'ordenActiva', 'reservacion'])
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->categoria_id, fn ($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->activa, fn ($q) => $q->where('activa', true))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('numero', 'like', '%' . $request->search . '%')
                    ->orWhere('nombre', 'like', '%' . $request->search . '%');
            }));

        return MesaResource::collection($query->orderBy('numero')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'numero' => 'required|integer',
            'nombre' => 'nullable|string|max:255',
            'capacidad' => 'nullable|integer|min:1',
            'ubicacion_id' => 'nullable|exists:mesa_ubicaciones,id',
            'estado' => 'nullable|string|max:20',
            'activa' => 'boolean',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
            'pos_x' => 'nullable|integer',
            'pos_y' => 'nullable|integer',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $mesa = Mesa::create($validated);

        return new MesaResource($mesa->load(['categoria', 'ubicacion', 'sucursal']));
    }

    public function show(Mesa $mesa)
    {
        return new MesaResource($mesa->load(['categoria', 'ubicacion', 'sucursal', 'ordenActiva', 'reservacion']));
    }

    public function update(Request $request, Mesa $mesa)
    {
        $validated = $request->validate([
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'numero' => 'sometimes|integer',
            'nombre' => 'nullable|string|max:255',
            'capacidad' => 'sometimes|integer|min:1',
            'ubicacion_id' => 'nullable|exists:mesa_ubicaciones,id',
            'estado' => 'nullable|string|max:20',
            'activa' => 'boolean',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
            'pos_x' => 'nullable|integer',
            'pos_y' => 'nullable|integer',
        ]);

        $mesa->update($validated);

        return new MesaResource($mesa->load(['categoria', 'ubicacion', 'sucursal']));
    }

    public function destroy(Mesa $mesa)
    {
        $mesa->delete();
        return response()->json(['message' => 'Mesa eliminada.']);
    }
}
