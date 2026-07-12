<?php

namespace App\Http\Controllers;

use App\Exports\ProveedoresExport;
use App\Imports\ProveedoresImport;
use App\Models\Proveedor;
use App\Services\ProveedorService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProveedorController extends Controller
{
    public function __construct(
        protected ProveedorService $proveedorService
    ) {}

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $incluirInactivos = $request->boolean('incluir_inactivos');

        $query = Proveedor::query()->orderBy('nombre');
        if (!$incluirInactivos) {
            $query->activo();
        }
        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('email', 'like', "%{$buscar}%")
                  ->orWhere('telefono', 'like', "%{$buscar}%")
                  ->orWhere('rnc', 'like', "%{$buscar}%");
            });
        }

        $proveedores = $query->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'telefono'              => 'nullable|string|max:30',
            'direccion'             => 'nullable|string|max:255',
            'rnc'                   => 'nullable|string|max:20',
            'tipo_persona'          => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr'  => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
            'activo'                => 'boolean',
        ]);

        $this->proveedorService->create($data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function show(Proveedor $proveedore)
    {
        $proveedore->load('compras');
        return view('proveedores.show', compact('proveedore'));
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', compact('proveedore'));
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'telefono'              => 'nullable|string|max:30',
            'direccion'             => 'nullable|string|max:255',
            'rnc'                   => 'nullable|string|max:20',
            'tipo_persona'          => 'nullable|string|in:fisica,juridica',
            'sujeto_retencion_isr'  => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
            'activo'                => 'boolean',
        ]);

        $this->proveedorService->update($proveedore, $data);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente');
    }

    public function exportExcel()
    {
        return Excel::download(new ProveedoresExport, 'proveedores.xlsx');
    }

    public function showImportForm()
    {
        return view('proveedores.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new ProveedoresImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->route('proveedores.index')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedores importados correctamente.');
    }

    public function toggleActivo(Proveedor $proveedore)
    {
        $proveedor = $this->proveedorService->toggleActivo($proveedore);
        return response()->json([
            'success' => true,
            'activo'  => $proveedor->activo,
            'label'   => $proveedor->activo ? 'Activo' : 'Inactivo',
        ]);
    }

    public function destroy(Request $request, Proveedor $proveedore)
    {
        $result = $this->proveedorService->delete($proveedore);
        if ($request->expectsJson()) {
            return response()->json($result);
        }
        $type = $result['deactivated'] ? 'warning' : 'success';
        return redirect()->route('proveedores.index')->with($type, $result['message']);
    }

    public function pdf(Request $request)
    {
        return $this->proveedorService->pdf($request->all());
    }
}
