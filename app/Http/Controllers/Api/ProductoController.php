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
            'codigo_barras' => 'sometimes|string|max:100|unique:productos,codigo_barras,' . $producto->id,
            'descripcion' => 'nullable|string',
            'precio' => 'sometimes|numeric|min:0',
            'precio_compra' => 'sometimes|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50',
            'itbis_porcentaje' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'imagen' => 'nullable|string',
        ]);

        $producto->update($validated);

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
                $inner->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('codigo_barras', 'like', '%' . $request->search . '%');
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50',
            'itbis_porcentaje' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'imagen' => 'nullable|string',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $producto = Producto::create($validated);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }
}
