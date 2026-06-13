<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\MesaCategoria;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::deSucursal()->with('categoria')->orderBy('numero')->get();
        $categorias = MesaCategoria::orderBy('nombre')->get();
        return view('restaurante.mesas', compact('mesas', 'categorias'));
    }

    public function show(Mesa $mesa)
    {
        return response()->json($mesa->load('categoria'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion'    => 'nullable|string|max:100',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['estado'] = 'disponible';

        Mesa::create($data);

        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa agregada correctamente.');
    }

    public function update(Request $request, Mesa $mesa)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion'    => 'nullable|string|max:100',
            'estado'       => 'required|string|in:disponible,ocupada,reservada,inactiva',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
            'activa'       => 'nullable|boolean',
        ]);

        $mesa->update($data);

        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa actualizada.');
    }

    public function destroy(Mesa $mesa)
    {
        if ($mesa->estado === 'ocupada') {
            return back()->with('error', 'No se puede eliminar una mesa ocupada.');
        }
        $mesa->delete();
        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa eliminada.');
    }
}
