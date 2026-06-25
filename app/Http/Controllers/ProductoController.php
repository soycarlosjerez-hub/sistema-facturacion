<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Exports\ProductosExport;
use App\Imports\DynamicProductosImport;
use App\Imports\ProductosImport;
use App\Services\ProductoService;
use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductoController extends Controller
{
    public function __construct(
        protected ProductoService $productoService
    ) {}

    public function index(Request $request)
    {
        $productos = $this->productoService->listAll($request->all());
        return view('productos.index', compact('productos'));
    }

    public function showImportForm()
    {
        return view('productos.import');
    }

    public function uploadPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $hash = md5(uniqid());
        $path = $file->storeAs('imports/' . $hash, 'file.' . $file->getClientOriginalExtension(), 'local');

        $headers = [];
        $fullPath = Storage::disk('local')->path($path);
        $ext = strtolower($file->getClientOriginalExtension());

        if (in_array($ext, ['csv', 'txt'])) {
            $guessed = $this->detectDelimiter($fullPath);
            $handle = fopen($fullPath, 'r');
            if ($handle) {
                $firstLine = fgets($handle);
                fclose($handle);
                if ($firstLine) {
                    $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
                    $result = $this->parseHeaders($firstLine, $guessed);
                    $headers = $result['headers'];
                    $delimiter = $result['delimiter'];
                }
            }
            if (!isset($delimiter)) $delimiter = $guessed;
        } else {
            $delimiter = ',';
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
                $sheet = $reader->getActiveSheet();
                $row = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1', null, true, false)[0] ?? [];
                $headers = array_filter(array_map('trim', $row), fn($h) => !empty($h));
            } catch (\Exception $e) {
                $headers = [];
            }
        }

        if (empty($headers)) {
            Storage::disk('local')->deleteDirectory('imports/' . $hash);
            $preview = '';
            if (isset($firstLine)) {
                $raw = substr($firstLine, 0, 200);
                $preview = ' Contenido (primeros 200 bytes): ' . json_encode($raw);
            }
            return back()->with('error', 'No se pudieron leer los encabezados del archivo. Verifica que la primera fila tenga los nombres de las columnas separados por coma (,) o punto y coma (;).' . $preview);
        }

        $productFields = [
            'nombre' => 'Nombre *',
            'codigo_barras' => 'Código de Barras',
            'descripcion' => 'Descripción',
            'precio' => 'Precio *',
            'precio_compra' => 'Precio de Compra',
            'unidad_medida' => 'Unidad de Medida',
            'itbis_porcentaje' => 'ITBIS %',
            'stock' => 'Stock *',
            'categoria' => 'Categoría (ID o Nombre)',
        ];

        return view('productos.import', [
            'step' => 'map',
            'hash' => $hash,
            'headers' => $headers,
            'productFields' => $productFields,
            'delimiter' => $delimiter,
        ]);
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'hash' => 'required|string',
            'delimiter' => ['required', Rule::in([',', ';'])],
            'mapping' => 'required|array',
            'mapping.*' => 'nullable|string',
        ]);

        $hash = $request->input('hash');
        $delimiter = $request->input('delimiter');
        $mapping = array_filter($request->input('mapping', []), fn($v) => !empty($v));
        $files = Storage::disk('local')->files('imports/' . $hash);

        if (empty($files)) {
            return redirect()->route('productos.import')
                ->with('error', 'Archivo no encontrado. Por favor sube el archivo nuevamente.');
        }

        $path = Storage::disk('local')->path($files[0]);

        $defaults = [
            'unidad_medida' => 'Unidad',
            'itbis_porcentaje' => 18,
            'stock' => 0,
            'precio' => 0,
        ];

        $import = new DynamicProductosImport($mapping, $defaults, $delimiter);

        try {
            Excel::import($import, $path);

            Storage::disk('local')->deleteDirectory('imports/' . $hash);

            $message = $import->imported . ' productos importados correctamente.';
            if (!empty($import->failures)) {
                $message .= ' ' . count($import->failures) . ' filas con errores fueron omitidas.';
            }

            return redirect()->route('productos.index')
                ->with('success', $message);
        } catch (\Throwable $e) {
            Storage::disk('local')->deleteDirectory('imports/' . $hash);
            $message = 'Error al importar: ' . $e->getMessage();
            if (!empty($import->failures)) {
                $message .= ' (' . count($import->failures) . ' filas omitidas)';
            }
            return redirect()->route('productos.index')
                ->with('error', $message);
        }
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
        // Apply tenant isolation
        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }

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

    private function detectDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) return ',';
        $firstLine = fgets($handle);
        fclose($handle);
        if (!$firstLine) return ',';

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $semicolons = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');

        return $semicolons > $commas ? ';' : ',';
    }

    private function parseHeaders(string $line, string $preferred): array
    {
        $attempts = $preferred === ',' ? [',', ';'] : [';', ','];

        foreach ($attempts as $d) {
            $headers = str_getcsv($line, $d);
            $headers = array_map(fn($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)), $headers);
            $headers = array_filter($headers, fn($h) => $h !== '');
            $headers = array_values($headers);
            if (!empty($headers)) {
                return ['headers' => $headers, 'delimiter' => $d];
            }
        }

        return ['headers' => [], 'delimiter' => $preferred];
    }
}
