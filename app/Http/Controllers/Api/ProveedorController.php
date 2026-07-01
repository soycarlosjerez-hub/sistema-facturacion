<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProveedorResource;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
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
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'rnc' => 'nullable|string|max:20',
            'tipo_persona' => 'nullable|string|max:20',
            'sujeto_retencion_isr' => 'nullable|boolean',
            'sujeto_retencion_itbis' => 'nullable|boolean',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $proveedor = Proveedor::create($validated);

        return new ProveedorResource($proveedor->load('compras'));
    }

    public function show(Proveedor $proveedor)
    {
        return new ProveedorResource($proveedor->load('compras'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'rnc' => 'sometimes|string|max:20',
            'tipo_persona' => 'nullable|string|max:20',
            'sujeto_retencion_isr' => 'nullable|boolean',
            'sujeto_retencion_itbis' => 'nullable|boolean',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $proveedor->update($validated);

        return new ProveedorResource($proveedor->load('compras'));
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return response()->json(['message' => 'Proveedor eliminado.']);
    }
}
