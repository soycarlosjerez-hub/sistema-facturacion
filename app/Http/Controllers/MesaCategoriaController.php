<?php

namespace App\Http\Controllers;

use App\Models\MesaCategoria;
use App\Models\Mesa;
use Illuminate\Http\Request;

class MesaCategoriaController extends Controller
{
    public function index()
    {
        $categorias = MesaCategoria::orderBy('orden')->orderBy('nombre')->get();
        return view('restaurante.categorias', compact('categorias'));
    }

    public function show(MesaCategoria $categoria)
    {
        return response()->json($categoria);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'color'  => 'required|string|max:7',
            'icono'  => 'nullable|string|max:50',
            'orden'  => 'nullable|integer|min:0',
        ]);

        MesaCategoria::create($data);

        return redirect()->route('restaurante.categorias.index')->with('success', 'Categoría creada.');
    }

    public function update(Request $request, MesaCategoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'color'  => 'required|string|max:7',
            'icono'  => 'nullable|string|max:50',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $categoria->update($data);

        return redirect()->route('restaurante.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(MesaCategoria $categoria)
    {
        Mesa::where('categoria_id', $categoria->id)->update(['categoria_id' => null]);
        $categoria->delete();
        return redirect()->route('restaurante.categorias.index')->with('success', 'Categoría eliminada.');
    }
}
