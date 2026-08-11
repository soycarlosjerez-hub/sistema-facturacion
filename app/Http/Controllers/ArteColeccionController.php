<?php

namespace App\Http\Controllers;

use App\Models\ArteColeccion;
use Illuminate\Http\Request;

class ArteColeccionController extends Controller
{
    public function index(Request $request)
    {
        $query = ArteColeccion::withCount('obras');

        if ($q = $request->get('q')) {
            $query->where(fn($b) => $b
                ->where('nombre', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%"));
        }

        $colecciones = $query->orderBy('orden')->orderBy('nombre')->paginate(15)->withQueryString();
        return view('arte.colecciones.index', compact('colecciones'));
    }

    public function create()
    {
        return redirect()->route('arte.colecciones.index');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        ArteColeccion::create($data);
        return redirect()->route('arte.colecciones.index')->with('success', 'Colección creada correctamente.');
    }

    public function edit(ArteColeccion $coleccion)
    {
        return redirect()->route('arte.colecciones.index');
    }

    public function update(Request $request, ArteColeccion $coleccion)
    {
        $coleccion->update($this->validar($request));
        return redirect()->route('arte.colecciones.index')->with('success', 'Colección actualizada correctamente.');
    }

    public function destroy(ArteColeccion $coleccion)
    {
        $coleccion->delete();
        return redirect()->route('arte.colecciones.index')->with('success', 'Colección eliminada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'tipo' => 'nullable|string|max:50',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);
    }
}