<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProveedorResource;
use App\Models\Proveedor;
use App\Traits\TenantAccess;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    use TenantAccess;
    public function index(Request $request)
    {
        $query = Proveedor::with(['compras'])
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('rnc', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            }))
            ->when($request->sujeto_retencion_isr, fn ($q) => $q->where('sujeto_retencion_isr', $request->sujeto_retencion_isr));

        return ProveedorResource::collection($query->orderBy('nombre')->paginate(15));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->business_instance_id;
        $nombreRules = 'required|string|max:255';
        if ($tenantId) {
            $nombreRules .= '|unique:proveedores,nombre,NULL,id,tenant_id,' . $tenantId;
        } else {
            $nombreRules .= '|unique:proveedores,nombre';
        }

        $validated = $request->validate([
            'nombre' => $nombreRules,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'rnc' => 'nullable|string|max:20',
            'tipo_persona' => 'required|string|in:fisica,juridica',
            'sujeto_retencion_isr' => 'nullable|boolean',
            'sujeto_retencion_itbis' => 'nullable|boolean',
        ], [
            'tipo_persona.required' => 'Debe seleccionar el tipo de persona (Física o Jurídica).',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $proveedor = Proveedor::create($validated);

        return new ProveedorResource($proveedor->load('compras'));
    }

    public function show(Proveedor $proveedor)
    {
        $this->requireTenantOwnership($proveedor);
        return new ProveedorResource($proveedor->load('compras'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->requireTenantOwnership($proveedor);

        $tenantId = auth()->user()->business_instance_id;
        $nombreRules = 'sometimes|string|max:255';
        if ($tenantId) {
            $nombreRules .= '|unique:proveedores,nombre,' . $proveedor->id . ',id,tenant_id,' . $tenantId;
        } else {
            $nombreRules .= '|unique:proveedores,nombre,' . $proveedor->id;
        }

        $validated = $request->validate([
            'nombre' => $nombreRules,
            'email' => 'sometimes|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'rnc' => 'sometimes|string|max:20',
            'tipo_persona' => 'sometimes|string|in:fisica,juridica',
            'sujeto_retencion_isr' => 'nullable|boolean',
            'sujeto_retencion_itbis' => 'nullable|boolean',
        ]);

        if (isset($validated['tipo_persona']) && $validated['tipo_persona'] === '') {
            $validated['tipo_persona'] = $proveedor->tipo_persona ?: 'juridica';
        }

        $proveedor->update($validated);

        return new ProveedorResource($proveedor->load('compras'));
    }

    public function destroy(Proveedor $proveedor)
    {
        $this->requireTenantOwnership($proveedor);
        $proveedor->delete();
        return response()->json(['message' => 'Proveedor eliminado.']);
    }
}
