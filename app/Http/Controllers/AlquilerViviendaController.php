<?php

namespace App\Http\Controllers;

use App\Models\AlquilerVivienda;
use Illuminate\Http\Request;

class AlquilerViviendaController extends Controller
{
    public function index()
    {
        $instanceId = auth()->user()->business_instance_id;
        $viviendas = AlquilerVivienda::porInstancia($instanceId)
            ->with('contratoActivo')
            ->orderBy('nombre')
            ->get();
        return view('alquileres.viviendas.index', compact('viviendas'));
    }

    public function create()
    {
        return view('alquileres.viviendas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|string|in:apartamento,casa,local,habitacion,oficina,otro',
            'habitaciones' => 'nullable|integer|min:0|max:50',
            'banos' => 'nullable|integer|min:0|max:50',
            'area_m2' => 'nullable|numeric|min:0',
            'monto_alquiler' => 'required|numeric|min:0',
            'monto_deposito' => 'nullable|numeric|min:0',
            'estado' => 'required|string|in:disponible,alquilado,mantenimiento,inactivo',
        ]);

        $data['business_instance_id'] = auth()->user()->business_instance_id;
        $data['monto_deposito'] = $data['monto_deposito'] ?? 0;
        $data['habitaciones'] = $data['habitaciones'] ?? 0;
        $data['banos'] = $data['banos'] ?? 0;

        AlquilerVivienda::create($data);

        return redirect()->route('alquileres.viviendas.index')
            ->with('success', 'Vivienda creada correctamente.');
    }

    public function edit($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $vivienda = AlquilerVivienda::porInstancia($instanceId)->findOrFail($id);
        return view('alquileres.viviendas.edit', compact('vivienda'));
    }

    public function update(Request $request, $id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $vivienda = AlquilerVivienda::porInstancia($instanceId)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|string|in:apartamento,casa,local,habitacion,oficina,otro',
            'habitaciones' => 'nullable|integer|min:0|max:50',
            'banos' => 'nullable|integer|min:0|max:50',
            'area_m2' => 'nullable|numeric|min:0',
            'monto_alquiler' => 'required|numeric|min:0',
            'monto_deposito' => 'nullable|numeric|min:0',
            'estado' => 'required|string|in:disponible,alquilado,mantenimiento,inactivo',
        ]);

        $data['monto_deposito'] = $data['monto_deposito'] ?? 0;
        $data['habitaciones'] = $data['habitaciones'] ?? 0;
        $data['banos'] = $data['banos'] ?? 0;

        $vivienda->update($data);

        return redirect()->route('alquileres.viviendas.index')
            ->with('success', 'Vivienda actualizada correctamente.');
    }

    public function destroy($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $vivienda = AlquilerVivienda::porInstancia($instanceId)->findOrFail($id);

        if ($vivienda->contratos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'No se puede eliminar una vivienda con contratos activos.');
        }

        $vivienda->delete();

        return redirect()->route('alquileres.viviendas.index')
            ->with('success', 'Vivienda eliminada correctamente.');
    }
}
