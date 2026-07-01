<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SucursalResource;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $query = Sucursal::with(['usuarios', 'ventas', 'compras', 'cajas', 'gastos', 'mesas'])
            ->when($request->activa, fn ($q) => $q->where('activa', true))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('codigo', 'like', '%' . $request->search . '%');
            }));

        return SucursalResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'rnc' => 'nullable|string|max:20',
            'activa' => 'boolean',
            'es_matriz' => 'boolean',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $sucursal = Sucursal::create($validated);

        return new SucursalResource($sucursal->load(['usuarios', 'ventas', 'compras', 'cajas', 'gastos', 'mesas']));
    }

    public function show(Sucursal $sucursal)
    {
        return new SucursalResource($sucursal->load(['usuarios', 'ventas', 'compras', 'cajas', 'gastos', 'mesas']));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $validated = $request->validate([
            'codigo' => 'sometimes|string|max:50',
            'nombre' => 'sometimes|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'email' => 'sometimes|email|max:255',
            'rnc' => 'sometimes|string|max:20',
            'activa' => 'boolean',
            'es_matriz' => 'boolean',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $sucursal->update($validated);

        return new SucursalResource($sucursal->load(['usuarios', 'ventas', 'compras', 'cajas', 'gastos', 'mesas']));
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();
        return response()->json(['message' => 'Sucursal eliminada.']);
    }
}
