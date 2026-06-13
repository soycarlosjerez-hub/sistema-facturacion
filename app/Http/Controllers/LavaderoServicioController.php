<?php

namespace App\Http\Controllers;

use App\Models\LavaderoServicio;
use Illuminate\Http\Request;

class LavaderoServicioController extends Controller
{
    public function index()
    {
        $servicios = LavaderoServicio::orderBy('orden')->orderBy('nombre')->get();
        return view('lavadero.servicios.index', compact('servicios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'duracion_minutos' => 'nullable|integer|min:1',
            'categoria' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);

        LavaderoServicio::create($data);
        return redirect()->route('lavadero.servicios.index')->with('success', 'Servicio creado');
    }

    public function update(Request $request, LavaderoServicio $servicio)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'duracion_minutos' => 'nullable|integer|min:1',
            'categoria' => 'nullable|string|max:100',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);

        $servicio->update($data);
        return redirect()->route('lavadero.servicios.index')->with('success', 'Servicio actualizado');
    }

    public function destroy(LavaderoServicio $servicio)
    {
        $servicio->delete();
        return redirect()->route('lavadero.servicios.index')->with('success', 'Servicio eliminado');
    }
}
