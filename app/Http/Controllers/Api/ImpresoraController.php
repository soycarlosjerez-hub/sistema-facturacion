<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImpresoraResource;
use App\Models\Impresora;
use Illuminate\Http\Request;

class ImpresoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Impresora::query()
            ->when($request->activa, fn ($q) => $q->where('activa', true))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->search, fn ($q) => $q->where('nombre', 'like', '%' . $request->search . '%'));

        return ImpresoraResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:50',
            'sucursal_id' => 'required|exists:sucursales,id',
            'puerto' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:50',
            'activa' => 'boolean',
            'configuracion' => 'nullable|array',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $impresora = Impresora::create($validated);

        return new ImpresoraResource($impresora);
    }

    public function show(Impresora $impresora)
    {
        return new ImpresoraResource($impresora);
    }

    public function update(Request $request, Impresora $impresora)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|string|max:50',
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'puerto' => 'nullable|string|max:100',
            'ip' => 'nullable|string|max:50',
            'activa' => 'boolean',
            'configuracion' => 'nullable|array',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $impresora->update($validated);

        return new ImpresoraResource($impresora);
    }

    public function destroy(Impresora $impresora)
    {
        $impresora->delete();
        return response()->json(['message' => 'Impresora eliminada.']);
    }
}
