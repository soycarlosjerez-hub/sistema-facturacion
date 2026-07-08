<?php

namespace App\Http\Controllers;

use App\Models\TattooArtist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TattooArtistController extends Controller
{
    public function index()
    {
        $artistas = TattooArtist::orderBy('nombre_completo')->get();
        return view('tattoo.artistas.index', compact('artistas'));
    }

    public function show(TattooArtist $artista)
    {
        $artista->load(['appointments' => fn($q) => $q->latest()->limit(20), 'designs']);
        return view('tattoo.artistas.show', compact('artista'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('tattoo.artistas.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'user_id'         => 'nullable|exists:users,id',
            'especialidad'    => 'nullable|string|max:100',
            'foto_perfil'     => 'nullable|string|max:255',
            'experiencia_anos'=> 'nullable|integer|min:0|max:99',
            'telefono'        => 'nullable|string|max:30',
            'whatsapp'        => 'nullable|string|max:30',
            'instagram'       => 'nullable|string|max:100',
            'comision_pct'    => 'required|numeric|min:0|max:100',
            'biografia'       => 'nullable|string|max:1000',
            'tipo'            => 'required|in:empleado,externo',
            'notas'           => 'nullable|string|max:500',
        ]);

        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
        $data['activo'] = $request->boolean('activo', true);

        TattooArtist::create($data);

        return redirect()->route('tattoo.artistas.index')
            ->with('success', 'Artista creado correctamente.');
    }

    public function edit(TattooArtist $artista)
    {
        $users = User::orderBy('name')->get();
        return view('tattoo.artistas.edit', compact('artista', 'users'));
    }

    public function update(Request $request, TattooArtist $artista)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'user_id'         => 'nullable|exists:users,id',
            'especialidad'    => 'nullable|string|max:100',
            'foto_perfil'     => 'nullable|string|max:255',
            'experiencia_anos'=> 'nullable|integer|min:0|max:99',
            'telefono'        => 'nullable|string|max:30',
            'whatsapp'        => 'nullable|string|max:30',
            'instagram'       => 'nullable|string|max:100',
            'comision_pct'    => 'required|numeric|min:0|max:100',
            'biografia'       => 'nullable|string|max:1000',
            'tipo'            => 'required|in:empleado,externo',
            'notas'           => 'nullable|string|max:500',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        $artista->update($data);

        return redirect()->route('tattoo.artistas.index')
            ->with('success', 'Artista actualizado correctamente.');
    }

    public function destroy(TattooArtist $artista)
    {
        $artista->delete();
        return redirect()->route('tattoo.artistas.index')
            ->with('success', 'Artista eliminado.');
    }

    public function toggleStatus(TattooArtist $artista)
    {
        $artista->update(['activo' => !$artista->activo]);
        return back()->with('success', 'Estado del artista actualizado.');
    }
}
