<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Exports\ProductosExport;
use App\Imports\ProductosImport;
use App\Services\ProductoService;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function __construct(
        protected ProductoService $productoService
    ) {}

    public function index(Request $request)
    {
        $productos = $this->productoService->list($request->all());
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

    public function store(StoreProductoRequest $request)
    {
        $this->productoService->create($request->validated(), $request->file('imagen'));

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

    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $this->productoService->update($producto, $request->validated(), $request->file('imagen'));

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $result = $this->productoService->delete($producto);

        return redirect()->route('productos.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
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
