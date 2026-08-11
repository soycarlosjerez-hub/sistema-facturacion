<?php

namespace App\Http\Controllers;

use App\Models\ArteArtista;
use Illuminate\Http\Request;

class ArteArtistaController extends Controller
{
    public function index(Request $request)
    {
        $query = ArteArtista::withCount('obras');

        if ($q = $request->get('q')) {
            $query->where(fn($b) => $b
                ->where('nombre', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('nacionalidad', 'like', "%{$q}%"));
        }

        $artistas = $query->orderBy('nombre')->paginate(15)->withQueryString();
        return view('arte.artistas.index', compact('artistas'));
    }

    public function create()
    {
        return redirect()->route('arte.artistas.index');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('arte-artistas', 'public');
        }

        ArteArtista::create($data);
        return redirect()->route('arte.artistas.index')->with('success', 'Artista creado correctamente.');
    }

    public function edit(ArteArtista $artista)
    {
        return redirect()->route('arte.artistas.index');
    }

    public function update(Request $request, ArteArtista $artista)
    {
        $data = $this->validar($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('arte-artistas', 'public');
        }

        $artista->update($data);
        return redirect()->route('arte.artistas.index')->with('success', 'Artista actualizado correctamente.');
    }

    public function destroy(ArteArtista $artista)
    {
        $artista->delete();
        return redirect()->route('arte.artistas.index')->with('success', 'Artista eliminado.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:200',
            'email' => 'nullable|email|max:200',
            'telefono' => 'nullable|string|max:50',
            'bio' => 'nullable|string',
            'nacionalidad' => 'nullable|string|max:100',
            'ano_nacimiento' => 'nullable|integer|min:1000|max:2100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'activo' => 'boolean',
            'orden' => 'nullable|integer',
            'notas' => 'nullable|string',
        ]);
    }
}