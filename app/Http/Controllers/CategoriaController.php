<?php

namespace App\Http\Controllers;

use App\Exports\CategoriaExport;
use App\Imports\CategoriaImport;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CategoriaController extends Controller
{
    public function index()
    {
        $query = Categoria::withCount('productos')->orderBy('nombre');
        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }
        $categorias = $query->get();
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->business_instance_id;
        $nombreRules = 'required|string|max:100';
        if ($tenantId) {
            $nombreRules .= '|unique:categorias,nombre,NULL,id,tenant_id,' . $tenantId;
        } else {
            $nombreRules .= '|unique:categorias,nombre';
        }

        $data = $request->validate([
            'nombre'      => $nombreRules,
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
        ]);

        $data['activa'] = $request->boolean('activa', true);
        // Assign to current tenant
        $data['tenant_id'] = auth()->user()->business_instance_id;

        Categoria::create($data);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categoria)
    {
        $categoria->load('productos');
        return view('categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        $tenantId = auth()->user()->business_instance_id;
        $productos = \App\Models\Producto::select('id', 'nombre', 'categoria_id')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('nombre')
            ->get();

        return view('categorias.edit', compact('categoria', 'productos'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $tenantId = auth()->user()->business_instance_id;
        $nombreRules = 'required|string|max:100';
        if ($tenantId) {
            $nombreRules .= '|unique:categorias,nombre,' . $categoria->id . ',id,tenant_id,' . $tenantId;
        } else {
            $nombreRules .= '|unique:categorias,nombre,' . $categoria->id;
        }

        $data = $request->validate([
            'nombre'      => $nombreRules,
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
            'productos'   => 'nullable|array',
            'productos.*' => 'integer|exists:productos,id',
        ]);

        $data['activa'] = $request->boolean('activa');
        unset($data['productos']);

        $categoria->update($data);

        // Sync products: detach all from this category, then attach selected ones
        $productosIds = $request->input('productos', []);
        \App\Models\Producto::where('categoria_id', $categoria->id)
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->update(['categoria_id' => null]);

        if (!empty($productosIds)) {
            \App\Models\Producto::whereIn('id', $productosIds)
                ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
                ->update(['categoria_id' => $categoria->id]);
        }

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function toggleActiva(Categoria $categoria)
    {
        $categoria->update(['activa' => !$categoria->activa]);
        return response()->json([
            'success' => true,
            'activa'  => $categoria->fresh()->activa,
            'label'   => $categoria->activa ? 'Activa' : 'Inactiva',
        ]);
    }

    public function exportExcel()
    {
        return Excel::download(new CategoriaExport, 'categorias.xlsx');
    }

    public function showImportForm()
    {
        return view('categorias.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        try {
            Excel::import(new CategoriaImport, $request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->route('categorias.index')
                ->with('error', 'Error al importar: ' . $e->getMessage());
        }

        return redirect()->route('categorias.index')
            ->with('success', 'Categorías importadas correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
