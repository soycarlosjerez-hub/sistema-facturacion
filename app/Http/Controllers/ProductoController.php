<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\DetalleCompra;
use App\Models\Compra;
use App\Exports\ProductosExport;
use App\Imports\ProductosImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('nombre')) {
            $termino = trim($request->nombre);
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('codigo_barras', 'like', '%' . $termino . '%');
            });
        }

        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float) $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'critical':
                    $query->where('stock', '<=', 5);
                    break;
                case 'low':
                    $query->whereBetween('stock', [6, 15]);
                    break;
                case 'ok':
                    $query->where('stock', '>', 15);
                    break;
            }
        }

        $productos = $query->with('categoria')->latest()->paginate(10)->appends($request->all());

        return view('productos.index', compact('productos'));
    }

    public function showImportForm()
    {
        return view('productos.import');
    }

    public function create()
    {
        $producto = new Producto();
        $categorias = Categoria::activas()->orderBy('nombre')->get();
        return view('productos.create', compact('producto', 'categorias'));
    }

    public function store(Request $request)
    {
        $data = $this->validateProducto($request);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $this->guardarImagen($request->file('imagen'));
        }

        $data['itbis_porcentaje'] = $data['itbis_porcentaje'] ?? 18.00;

        Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['detallesCompras.compra.proveedor', 'ventaDetalles.venta.cliente']);
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::activas()->orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $this->validateProducto($request, $producto->id);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $this->guardarImagen($request->file('imagen'));
        }

        $data['itbis_porcentaje'] = $data['itbis_porcentaje'] ?? 18.00;

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->ventaDetalles()->exists()) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto "' . $producto->nombre . '" porque tiene ventas asociadas.');
        }

        $compraIds = DetalleCompra::where('producto_id', $producto->id)
            ->pluck('compra_id')
            ->unique();

        DetalleCompra::where('producto_id', $producto->id)->delete();

        foreach ($compraIds as $compraId) {
            $remaining = DetalleCompra::where('compra_id', $compraId)->count();
            if ($remaining === 0) {
                Compra::where('id', $compraId)->delete();
            }
        }

        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ProductosExport($this->buildFilteredQuery($request)), 'productos.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $productos = $this->buildFilteredQuery($request)->get();
        $pdf = Pdf::loadView('productos.pdf', compact('productos'));
        return $pdf->download('productos.pdf');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new ProductosImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }

        return redirect()->route('productos.index')
            ->with('success', 'Productos importados correctamente.');
    }

    protected function validateProducto(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = $ignoreId
            ? 'unique:productos,codigo_barras,' . $ignoreId
            : 'unique:productos,codigo_barras';

        return $request->validate([
            'nombre'           => 'required|string|max:255',
            'codigo_barras'    => ['nullable', 'string', 'max:100', $uniqueRule],
            'descripcion'      => 'nullable|string|max:1000',
            'precio'           => 'required|numeric|min:0|max:9999999.99',
            'precio_compra'    => 'nullable|numeric|min:0|max:9999999.99',
            'unidad_medida'    => 'nullable|string|max:50',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'stock'            => 'required|integer|min:0',
            'stock_minimo'     => 'nullable|integer|min:0',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'categoria_id'     => 'nullable|exists:categorias,id',
        ], [
            'nombre.required'        => 'El nombre del producto es obligatorio.',
            'precio.required'        => 'El precio de venta es obligatorio.',
            'precio.numeric'         => 'El precio debe ser un número válido.',
            'precio.min'             => 'El precio no puede ser negativo.',
            'precio_compra.numeric'  => 'El precio de compra debe ser un número válido.',
            'stock.required'         => 'El stock es obligatorio.',
            'stock.integer'          => 'El stock debe ser un número entero.',
            'stock.min'              => 'El stock no puede ser negativo.',
            'imagen.image'           => 'El archivo debe ser una imagen válida.',
            'imagen.mimes'           => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'imagen.max'             => 'La imagen no puede superar los 2 MB.',
            'codigo_barras.unique'   => 'Este código de barras ya está registrado.',
        ]);
    }

    protected function guardarImagen($file): string
    {
        $nombre = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        return $file->storeAs('productos', $nombre . '-' . uniqid() . '.' . $extension, 'public');
    }

    protected function buildFilteredQuery(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('nombre')) {
            $termino = trim($request->nombre);
            $query->where(function ($q) use ($termino) {
                $q->where('nombre', 'like', '%' . $termino . '%')
                  ->orWhere('codigo_barras', 'like', '%' . $termino . '%');
            });
        }

        if ($request->filled('precio_min')) {
            $query->where('precio', '>=', (float) $request->precio_min);
        }

        if ($request->filled('precio_max')) {
            $query->where('precio', '<=', (float) $request->precio_max);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'critical':
                    $query->where('stock', '<=', 5);
                    break;
                case 'low':
                    $query->whereBetween('stock', [6, 15]);
                    break;
                case 'ok':
                    $query->where('stock', '>', 15);
                    break;
            }
        }

        return $query;
    }
}
