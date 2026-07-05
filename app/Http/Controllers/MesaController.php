<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\MesaCategoria;
use App\Models\MesaUbicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesaController extends Controller
{
    public function index()
    {
        $mesasAll = Mesa::deSucursal()->with('categoria', 'ubicacion')->orderBy('numero')->get();
        $categorias = MesaCategoria::orderBy('nombre')->get();
        $ubicaciones = MesaUbicacion::orderBy('nombre')->get();
        return view('restaurante.mesas', compact('mesasAll', 'categorias', 'ubicaciones'));
    }

    public function show(Mesa $mesa)
    {
        return response()->json($mesa->load('categoria', 'ubicacion'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion_id' => 'nullable|exists:mesa_ubicaciones,id',
            'categoria_id' => 'nullable|exists:mesa_categorias,id',
        ]);

        $data['sucursal_id'] = session('sucursal_id');
        $data['estado'] = 'disponible';
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;

        Mesa::create($data);

        return redirect()->route('restaurante.mesas.index')->with('success', 'Mesa agregada correctamente.');
    }

    public function update(Request $request, Mesa $mesa)
    {
        $data = $request->validate([
            'numero'       => 'required|string|max:20',
            'nombre'       => 'nullable|string|max:100',
            'capacidad'    => 'required|integer|min:1',
            'ubicacion_id' => 'nullable|exists:mesa_ubicaciones,id',
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
