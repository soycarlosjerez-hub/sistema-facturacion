<?php

namespace App\Http\Controllers;

use App\Models\ArteObra;
use App\Models\ArteArtista;
use App\Models\ArteColeccion;
use Illuminate\Http\Request;

class ArteObraController extends Controller
{
    public function index(Request $request)
    {
        $query = ArteObra::with(['artista', 'coleccion']);

        if ($q = $request->get('q')) {
            $query->where(fn($b) => $b
                ->where('titulo', 'like', "%{$q}%")
                ->orWhere('tecnica', 'like', "%{$q}%")
                ->orWhereHas('artista', fn($a) => $a->where('nombre', 'like', "%{$q}%")));
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        if ($artistaId = $request->get('artista')) {
            $query->where('artista_id', $artistaId);
        }

        if ($coleccionId = $request->get('coleccion')) {
            $query->where('coleccion_id', $coleccionId);
        }

        $obras = $query->orderBy('orden')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('arte.obras.index', [
            'obras' => $obras,
            'artistas' => ArteArtista::activos()->orderBy('nombre')->get(),
            'colecciones' => ArteColeccion::activos()->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('arte.obras.create', [
            'artistas' => ArteArtista::activos()->orderBy('nombre')->get(),
            'colecciones' => ArteColeccion::activos()->orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('arte-obras', 'public');
        }

        ArteObra::create($data);
        return redirect()->route('arte.obras.index')->with('success', 'Obra creada correctamente.');
    }

    public function show(ArteObra $obra)
    {
        $obra->load(['artista', 'coleccion', 'exhibiciones', 'consignacion']);
        return view('arte.obras.show', compact('obra'));
    }

    public function edit(ArteObra $obra)
    {
        return view('arte.obras.edit', [
            'obra' => $obra,
            'artistas' => ArteArtista::activos()->orderBy('nombre')->get(),
            'colecciones' => ArteColeccion::activos()->orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, ArteObra $obra)
    {
        $data = $this->validar($request);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('arte-obras', 'public');
        }

        $obra->update($data);
        return redirect()->route('arte.obras.index')->with('success', 'Obra actualizada correctamente.');
    }

    public function destroy(ArteObra $obra)
    {
        $obra->delete();
        return redirect()->route('arte.obras.index')->with('success', 'Obra eliminada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo' => 'required|string|max:300',
            'descripcion' => 'nullable|string',
            'artista_id' => 'required|exists:arte_artistas,id',
            'coleccion_id' => 'nullable|exists:arte_colecciones,id',
            'tecnica' => 'nullable|string|max:150',
            'ano_creacion' => 'nullable|integer|min:1000|max:2100',
            'dimensiones' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:200',
            'precio_compra' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'estado' => 'required|in:vendida,disponible,en_exhibicion,en_consulta',
            'fecha_adquisicion' => 'nullable|date',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
        ]);
    }
}