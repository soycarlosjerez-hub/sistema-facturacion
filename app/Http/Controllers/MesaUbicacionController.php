<?php

namespace App\Http\Controllers;

use App\Models\MesaUbicacion;
use Illuminate\Http\Request;

class MesaUbicacionController extends Controller
{
    public function index()
    {
        $query = MesaUbicacion::withCount('mesas')->orderBy('nombre');
        if (auth()->check() && auth()->user()->business_instance_id !== null) {
            $query->where('tenant_id', auth()->user()->business_instance_id);
        }
        $ubicaciones = $query->get();
        return view('restaurante.ubicaciones.index', compact('ubicaciones'));
    }

    public function show(MesaUbicacion $mesaUbicacion)
    {
        return response()->json($mesaUbicacion);
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->business_instance_id;
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
        ]);
        $data['activa'] = $request->boolean('activa', true);
        $data['tenant_id'] = $tenantId;

        MesaUbicacion::create($data);

        return redirect()->route('restaurante.ubicaciones.index')
            ->with('success', 'Ubicación creada correctamente.');
    }

    public function update(Request $request, MesaUbicacion $mesaUbicacion)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'activa'      => 'boolean',
        ]);
        $data['activa'] = $request->boolean('activa');

        $mesaUbicacion->update($data);

        return redirect()->route('restaurante.ubicaciones.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroy(MesaUbicacion $mesaUbicacion)
    {
        if ($mesaUbicacion->mesas()->exists()) {
            return back()->with('error', 'No se puede eliminar la ubicación porque tiene mesas asociadas.');
        }

        $mesaUbicacion->delete();

        return redirect()->route('restaurante.ubicaciones.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }
}
