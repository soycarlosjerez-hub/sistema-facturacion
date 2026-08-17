<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AlquilerResource;
use App\Models\Alquiler;
use App\Traits\TenantAccess;
use Illuminate\Http\Request;

class AlquilerController extends Controller
{
    use TenantAccess;
    public function index(Request $request)
    {
        $query = Alquiler::with(['cliente', 'sucursal', 'user', 'vehiculo'])
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado));

        return AlquilerResource::collection($query->orderBy('fecha_inicio', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50',
            'cliente_id' => 'required|exists:clientes,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'user_id' => 'required|exists:users,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|string|max:20',
            'total' => 'required|numeric|min:0',
            'deposito' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $alquiler = Alquiler::create($validated);

        return new AlquilerResource($alquiler->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function show(Alquiler $alquiler)
    {
        $this->requireTenantOwnership($alquiler);
        return new AlquilerResource($alquiler->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function update(Request $request, Alquiler $alquiler)
    {
        $this->requireTenantOwnership($alquiler);

        $validated = $request->validate([
            'estado' => 'sometimes|string|max:20',
            'fecha_fin' => 'sometimes|date|after:fecha_inicio',
            'total' => 'sometimes|numeric|min:0',
            'deposito' => 'sometimes|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        $alquiler->update($validated);

        return new AlquilerResource($alquiler->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function destroy(Alquiler $alquiler)
    {
        $this->requireTenantOwnership($alquiler);
        $alquiler->delete();
        return response()->json(['message' => 'Alquiler eliminado.']);
    }
}
