<?php

namespace App\Http\Controllers;

use App\Models\TattooDesign;
use App\Models\TattooArtist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TattooDesignController extends Controller
{
    public function index()
    {
        $disenos = TattooDesign::with('artist')->orderBy('created_at', 'desc')->get();
        $estilos = TattooDesign::whereNotNull('estilo')->distinct()->pluck('estilo');
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        return view('tattoo.disenos.index', compact('disenos', 'estilos', 'artistas'));
    }

    public function create()
    {
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        return view('tattoo.disenos.create', compact('artistas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'nullable|string|max:2000',
            'estilo'              => 'nullable|string|max:60',
            'imagen_portada'      => 'nullable|string|max:255',
            'galeria_imagenes'    => 'nullable|json',
            'precio_minimo'       => 'required|numeric|min:0',
            'precio_maximo'       => 'required|numeric|min:0|gte:precio_minimo',
            'duracion_estimada_min' => 'required|integer|min:15',
            'artist_id'           => 'nullable|exists:tattoo_artists,id',
        ]);

        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
        $data['popular'] = $request->boolean('popular', false);
        $data['activo'] = $request->boolean('activo', true);

        TattooDesign::create($data);

        return redirect()->route('tattoo.disenos.index')
            ->with('success', 'Diseño creado correctamente.');
    }

    public function edit(TattooDesign $diseno)
    {
        $artistas = TattooArtist::activos()->orderBy('nombre_completo')->get();
        return view('tattoo.disenos.edit', compact('diseno', 'artistas'));
    }

    public function update(Request $request, TattooDesign $diseno)
    {
        $data = $request->validate([
            'titulo'              => 'required|string|max:255',
            'descripcion'         => 'nullable|string|max:2000',
            'estilo'              => 'nullable|string|max:60',
            'imagen_portada'      => 'nullable|string|max:255',
            'galeria_imagenes'    => 'nullable|json',
            'precio_minimo'       => 'required|numeric|min:0',
            'precio_maximo'       => 'required|numeric|min:0|gte:precio_minimo',
            'duracion_estimada_min' => 'required|integer|min:15',
            'artist_id'           => 'nullable|exists:tattoo_artists,id',
        ]);

        $data['popular'] = $request->boolean('popular', false);
        $data['activo'] = $request->boolean('activo', true);

        $diseno->update($data);

        return redirect()->route('tattoo.disenos.index')
            ->with('success', 'Diseño actualizado correctamente.');
    }

    public function destroy(TattooDesign $diseno)
    {
        $diseno->delete();
        return redirect()->route('tattoo.disenos.index')
            ->with('success', 'Diseño eliminado.');
    }
}
