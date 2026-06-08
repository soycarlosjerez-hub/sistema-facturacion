<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    public function index()
    {
        $sucursales = Sucursal::orderBy('nombre')->get();
        return view('sucursales.index', compact('sucursales'));
    }

    public function create()
    {
        return view('sucursales.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:sucursales,codigo',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'rnc' => 'nullable|string|max:20',
            'activa' => 'nullable|boolean',
            'es_matriz' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->boolean('activa');
        $data['es_matriz'] = $request->boolean('es_matriz');

        Sucursal::create($data);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function edit(Sucursal $sucursal)
    {
        return view('sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:20|unique:sucursales,codigo,' . $sucursal->id,
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'rnc' => 'nullable|string|max:20',
            'activa' => 'nullable|boolean',
            'es_matriz' => 'nullable|boolean',
        ]);

        $data['activa'] = $request->boolean('activa');
        $data['es_matriz'] = $request->boolean('es_matriz');

        $sucursal->update($data);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $sucursal->delete();
        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada.');
    }
}
