<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $query = Categoria::withCount('productos')->orderBy('nombre');
        // Scope to current tenant
        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }
        $categorias = $query->paginate(10);
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
        return view('categorias.edit', compact('categoria'));
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
        ]);

        $data['activa'] = $request->boolean('activa');

        $categoria->update($data);

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
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
