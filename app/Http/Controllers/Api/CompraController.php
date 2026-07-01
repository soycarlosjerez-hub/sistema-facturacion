<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompraResource;
use App\Models\Compra;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'sucursal', 'almacen', 'user', 'tipoCompra', 'detalles.producto'])
            ->when($request->proveedor_id, fn ($q) => $q->where('proveedor_id', $request->proveedor_id))
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->fecha_desde, fn ($q) => $q->whereDate('fecha', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn ($q) => $q->whereDate('fecha', '<=', $request->fecha_hasta))
            ->when($request->search_folio, fn ($q) => $q->where('folio', 'like', '%' . $request->search_folio . '%'));

        return CompraResource::collection($query->orderBy('fecha', 'desc')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'almacen_id' => 'nullable|exists:almacenes,id',
            'user_id' => 'required|exists:users,id',
            'tipo_compra_id' => 'nullable|exists:tipos_compras,id',
            'total' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'itbis_total' => 'required|numeric|min:0',
            'fecha' => 'nullable|date',
            'observaciones' => 'nullable|string',
            'aplica_retencion_isr' => 'nullable|boolean',
            'aplica_retencion_itbis' => 'nullable|boolean',
            'retencion_isr' => 'nullable|numeric|min:0',
            'retencion_itbis' => 'nullable|numeric|min:0',
            'folio' => 'nullable|string|max:50',
            'detalles' => 'required|array',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.itbis_porcentaje' => 'required|numeric|min:0',
        ]);

        $compra = Compra::create($validated);

        foreach ($validated['detalles'] as $detalle) {
            $compra->detalles()->create(array_merge($detalle, ['compra_id' => $compra->id]));
        }

        return new CompraResource($compra->load(['proveedor', 'sucursal', 'almacen', 'user', 'detalles.producto']));
    }

    public function show(Compra $compra)
    {
        return new CompraResource($compra->load(['proveedor', 'sucursal', 'almacen', 'user', 'tipoCompra', 'detalles.producto']));
    }

    public function update(Request $request, Compra $compra)
    {
        $validated = $request->validate([
            'total' => 'sometimes|numeric|min:0',
            'subtotal' => 'sometimes|numeric|min:0',
            'itbis_total' => 'sometimes|numeric|min:0',
            'observaciones' => 'nullable|string',
            'folio' => 'sometimes|string|max:50',
        ]);

        $compra->update($validated);

        return new CompraResource($compra->load(['proveedor', 'sucursal', 'almacen', 'user', 'tipoCompra', 'detalles.producto']));
    }

    public function destroy(Compra $compra)
    {
        $compra->delete();
        return response()->json(['message' => 'Compra eliminada.']);
    }
}
