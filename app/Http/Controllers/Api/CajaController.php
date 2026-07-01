<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CajaResource;
use App\Models\Caja;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        $query = Caja::with(['sucursal', 'sesiones'])
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->activo, fn ($q) => $q->where('activo', true));

        return CajaResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'sucursal_id' => 'required|exists:sucursales,id',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $caja = Caja::create($validated);

        return new CajaResource($caja->load(['sucursal', 'sesiones']));
    }

    public function show(Caja $caja)
    {
        return new CajaResource($caja->load(['sucursal', 'sesiones', 'sesionActiva']));
    }

    public function update(Request $request, Caja $caja)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'codigo' => 'sometimes|string|max:50',
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'ubicacion' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:20',
            'activo' => 'boolean',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $caja->update($validated);

        return new CajaResource($caja->load(['sucursal', 'sesiones']));
    }

    public function destroy(Caja $caja)
    {
        $caja->delete();
        return response()->json(['message' => 'Caja eliminada.']);
    }
}
