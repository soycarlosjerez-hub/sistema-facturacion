<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductoResource;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with(['categoria', 'ingredientes'])
            ->when($request->categoria_id, fn ($q) => $q->where('categoria_id', $request->categoria_id))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('nombre', 'like', '%' . $request->search . '%')
                    ->orWhere('codigo_barras', 'like', '%' . $request->search . '%');
            }))
            ->when($request->low_stock, fn ($q) => $q->whereColumn('stock', '<=', 'stock_minimo'))
            ->when($request->out_of_stock, fn ($q) => $q->where('stock', 0));

        return ProductoResource::collection($query->orderBy('nombre')->paginate(15));
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
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $producto = Producto::create($validated);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function show(Producto $producto)
    {
        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function update(Request $request, Producto $producto)
    {
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
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $producto->update($validated);

        return new ProductoResource($producto->load(['categoria', 'ingredientes']));
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado.']);
    }
}
