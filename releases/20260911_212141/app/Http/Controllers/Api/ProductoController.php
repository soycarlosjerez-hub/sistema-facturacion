<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use App\Traits\TenantAccess;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    use TenantAccess;

    public function show(Producto $producto)
    {
        $this->requireTenantOwnership($producto);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function update(Request $request, Producto $producto)
    {
        $this->requireTenantOwnership($producto);

        $validated = $request->validate([
            'categoria_id' => 'sometimes|exists:categorias,id',
            'nombre' => 'sometimes|string|max:255',
            'codigo_barras' => 'sometimes|string|max:100|unique:productos,codigo_barras,'.$producto->id,
            'codigo_referencia' => 'sometimes|string|max:100|unique:productos,codigo_referencia,'.$producto->id,
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric|min:0',
            'precio_compra' => 'sometimes|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50',
            'itbis_porcentaje' => 'sometimes|numeric|min:0|max:100',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0|lte:stock',
            'activo' => 'sometimes|boolean',
            'serial_imei' => 'nullable|string|max:100|unique:productos,serial_imei,'.$producto->id,
            'requiere_serial' => 'sometimes|boolean',
            'vendible_imei' => 'sometimes|boolean',
            'es_licencia' => 'sometimes|boolean',
            'tipo_licencia' => 'nullable|string|max:50',
            'licencia_max_usuarios' => 'nullable|integer|min:1',
            'garantia_dias' => 'nullable|integer|min:0',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:200',
            'imagen' => 'nullable|string',
            'tipo_servicio' => 'sometimes|in:producto,servicio,general',
            'especializacion' => 'nullable|string|max:100',
            'almacenamiento_gb' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'precio_servicio' => 'nullable|numeric|min:0',
            'duracion_servicio_horas' => 'nullable|integer|min:0',
            'requires_setup' => 'sometimes|boolean',
            'marca_tecnologica_id' => 'nullable|exists:marca_tecnologicas,id',
        ]);

        $producto->update($validated);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'sometimes|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras',
            'codigo_referencia' => 'nullable|string|max:100|unique:productos,codigo_referencia',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'activo' => 'sometimes|boolean',
            'serial_imei' => 'nullable|string|max:100|unique:productos,serial_imei',
            'requiere_serial' => 'sometimes|boolean',
            'vendible_imei' => 'sometimes|boolean',
            'es_licencia' => 'sometimes|boolean',
            'tipo_licencia' => 'nullable|string|max:50',
            'licencia_max_usuarios' => 'nullable|integer|min:1',
            'garantia_dias' => 'nullable|integer|min:0',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:200',
            'imagen' => 'nullable|string',
            'tipo_servicio' => 'sometimes|in:producto,servicio,general',
            'especializacion' => 'nullable|string|max:100',
            'almacenamiento_gb' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'precio_servicio' => 'nullable|numeric|min:0',
            'duracion_servicio_horas' => 'nullable|integer|min:0',
            'requires_setup' => 'sometimes|boolean',
            'marca_tecnologica_id' => 'nullable|exists:marca_tecnologicas,id',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $producto = Producto::create($validated);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function destroy(Producto $producto)
    {
        $this->requireTenantOwnership($producto);
        $producto->delete();

        return response()->json(['message' => 'Producto eliminado.']);
    }

    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'ingredientes'])
            ->when($request->categoria_id, fn ($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('nombre', 'like', '%'.$request->search.'%')
                    ->orWhere('codigo_barras', 'like', '%'.$request->search.'%');
            }))
            ->when($request->low_stock, fn ($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->when($request->out_of_stock, fn ($q) => $q->where('stock', 0))
            ->when($request->stock_lte !== null && $request->stock_lte !== '', fn ($q) => $q->where('stock', '<=', (int) $request->stock_lte));

        $perPage = $request->input('per_page', 15);

        if ($perPage === 'all' || (int) $perPage === -1) {
            $productos = $query->orderBy('nombre')->get();

            return ProductoResource::collection($productos);
        }

        return ProductoResource::collection($query->orderBy('nombre')->paginate(min((int) $perPage, 100)));
    }
}
