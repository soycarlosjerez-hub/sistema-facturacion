<?php

namespace App\Http\Controllers;

use App\Models\AlquilerInquilino;
use Illuminate\Http\Request;

class AlquilerInquilinoController extends Controller
{
    public function index()
    {
        $instanceId = auth()->user()->business_instance_id;
        $inquilinos = AlquilerInquilino::porInstancia($instanceId)
            ->withCount('contratos')
            ->orderBy('nombre')
            ->get();
        return view('alquileres.inquilinos.index', compact('inquilinos'));
    }

    public function create()
    {
        return view('alquileres.inquilinos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'notas' => 'nullable|string',
        ]);

        $data['business_instance_id'] = auth()->user()->business_instance_id;

        AlquilerInquilino::create($data);

        return redirect()->route('alquileres.inquilinos.index')
            ->with('success', 'Inquilino creado correctamente.');
    }

    public function edit($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $inquilino = AlquilerInquilino::porInstancia($instanceId)->findOrFail($id);
        return view('alquileres.inquilinos.edit', compact('inquilino'));
    }

    public function update(Request $request, $id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $inquilino = AlquilerInquilino::porInstancia($instanceId)->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:500',
            'notas' => 'nullable|string',
        ]);

        $inquilino->update($data);

        return redirect()->route('alquileres.inquilinos.index')
            ->with('success', 'Inquilino actualizado correctamente.');
    }

    public function destroy($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $inquilino = AlquilerInquilino::porInstancia($instanceId)->findOrFail($id);

        if ($inquilino->contratos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'No se puede eliminar un inquilino con contratos activos.');
        }

        $inquilino->delete();

        return redirect()->route('alquileres.inquilinos.index')
            ->with('success', 'Inquilino eliminado correctamente.');
    }
}
