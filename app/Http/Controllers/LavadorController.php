<?php

namespace App\Http\Controllers;

use App\Models\Lavador;
use Illuminate\Http\Request;

class LavadorController extends Controller
{
    public function index()
    {
        $lavadores = Lavador::orderBy('activo', 'desc')->orderBy('nombre')->get();
        $defaultFijo = 30;
        $defaultTemporal = 50;
        return view('lavadero.lavadores.index', compact('lavadores', 'defaultFijo', 'defaultTemporal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'tipo' => 'required|in:fijo,temporal',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'identificacion' => 'nullable|string|max:30',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['activo'] = $request->boolean('activo', true);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        Lavador::create($data);
        return redirect()->route('lavadero.lavadores.index')->with('success', 'Lavador creado');
    }

    public function update(Request $request, Lavador $lavador)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:200',
            'tipo' => 'required|in:fijo,temporal',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'identificacion' => 'nullable|string|max:30',
            'activo' => 'boolean',
            'notas' => 'nullable|string',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $lavador->update($data);
        return redirect()->route('lavadero.lavadores.index')->with('success', 'Lavador actualizado');
    }

    public function destroy(Lavador $lavador)
    {
        $lavador->delete();
        return redirect()->route('lavadero.lavadores.index')->with('success', 'Lavador eliminado');
    }

    public function activos()
    {
        return response()->json(Lavador::activos()->orderBy('nombre')->get());
    }
}
