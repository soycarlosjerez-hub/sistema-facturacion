<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LavaderoResource;
use App\Models\Lavadero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LavaderoController extends Controller
{
    /**
     * Get the current tenant ID from the authenticated user.
     */
    protected function getCurrentTenantId(): ?int
    {
        return Auth::check() ? Auth::user()->business_instance_id : null;
    }

    public function index(Request $request)
    {
        $tenantId = $this->getCurrentTenantId();

        $query = Lavadero::with(['cliente', 'sucursal', 'user', 'vehiculo'])
            ->where('tenant_id', $tenantId)
            ->when($request->cliente_id, fn ($q) => $q->where('cliente_id', $request->cliente_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->estado, fn ($q) => $q->where('estado', $request->estado));

        return LavaderoResource::collection($query->orderBy('fecha_ingreso', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $tenantId = $this->getCurrentTenantId();

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

        $lavadero = Lavadero::create(array_merge($validated, [
            'tenant_id' => $tenantId,
        ]));

        return new LavaderoResource($lavadero->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function show(Lavadero $lavadero)
    {
        // Verify ownership: the lavadero record must belong to the authenticated user's tenant
        $tenantId = $this->getCurrentTenantId();
        if ($tenantId === null || (int) $lavadero->tenant_id !== $tenantId) {
            abort(404, 'Recurso no encontrado.');
        }

        return new LavaderoResource($lavadero->load(['cliente', 'sucursal', 'user', 'vehiculo']));
    }

    public function update(Request $request, Lavadero $lavadero)
    {
        // Verify ownership
        $tenantId = $this->getCurrentTenantId();
        if ($tenantId === null || (int) $lavadero->tenant_id !== $tenantId) {
            abort(404, 'Recurso no encontrado.');
        }

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
        // Verify ownership
        $tenantId = $this->getCurrentTenantId();
        if ($tenantId === null || (int) $lavadero->tenant_id !== $tenantId) {
            abort(404, 'Recurso no encontrado.');
        }

        $lavadero->delete();
        return response()->json(['message' => 'Registro de lavandería eliminado.']);
    }
}
