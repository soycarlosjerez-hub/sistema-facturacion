<?php

namespace App\Http\Controllers;

use App\Models\ArteExhibicion;
use App\Models\ArteObra;
use Illuminate\Http\Request;

class ArteExhibicionController extends Controller
{
    public function index(Request $request)
    {
        $query = ArteExhibicion::withCount('obras');

        if ($q = $request->get('q')) {
            $query->where(fn($b) => $b
                ->where('nombre', 'like', "%{$q}%")
                ->orWhere('ubicacion', 'like', "%{$q}%"));
        }

        $exhibiciones = $query->orderByDesc('fecha_inicio')->paginate(15)->withQueryString();
        return view('arte.exhibiciones.index', compact('exhibiciones'));
    }

    public function create()
    {
        return view('arte.exhibiciones.create', [
            'obrasDisponibles' => ArteObra::disponibles()->orderBy('titulo')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        $obraIds = $data['obra_ids'] ?? [];
        unset($data['obra_ids']);

        $exhibicion = ArteExhibicion::create($data);
        $this->syncObras($exhibicion, $obraIds);

        return redirect()->route('arte.exhibiciones.index')->with('success', 'Exhibición creada correctamente.');
    }

    public function show(ArteExhibicion $exhibicion)
    {
        $exhibicion->load(['obras.artista']);
        $obrasDisponibles = ArteObra::disponibles()->orderBy('titulo')->get();
        return view('arte.exhibiciones.show', [
            'exhibicion' => $exhibicion,
            'obrasDisponibles' => $obrasDisponibles,
        ]);
    }

    public function edit(ArteExhibicion $exhibicion)
    {
        $exhibicion->load('obras');
        return view('arte.exhibiciones.edit', [
            'exhibicion' => $exhibicion,
            'obrasDisponibles' => ArteObra::disponibles()->orderBy('titulo')->get(),
        ]);
    }

    public function update(Request $request, ArteExhibicion $exhibicion)
    {
        $data = $this->validar($request);

        $obraIds = $data['obra_ids'] ?? [];
        unset($data['obra_ids']);

        $exhibicion->update($data);
        $this->syncObras($exhibicion, $obraIds);

        return redirect()->route('arte.exhibiciones.index')->with('success', 'Exhibición actualizada correctamente.');
    }

    public function destroy(ArteExhibicion $exhibicion)
    {
        $exhibicion->delete();
        return redirect()->route('arte.exhibiciones.index')->with('success', 'Exhibición eliminada.');
    }

    public function attachObra(Request $request, ArteExhibicion $exhibicion)
    {
        $data = $request->validate([
            'obra_id' => 'required|exists:arte_obras,id',
            'ubicacion_en_sala' => 'nullable|string|max:200',
        ]);

        if ($exhibicion->obras()->where('arte_obras.id', $data['obra_id'])->exists()) {
            return back()->with('warning', 'La obra ya está asignada a esta exhibición.');
        }

        $exhibicion->obras()->attach($data['obra_id'], [
            'ubicacion_en_sala' => $data['ubicacion_en_sala'] ?? null,
            'fecha_asignacion' => now()->toDateString(),
        ]);

        ArteObra::where('id', $data['obra_id'])
            ->where('estado', 'disponible')
            ->update(['estado' => 'en_exhibicion']);

        return back()->with('success', 'Obra asignada a la exhibición.');
    }

    public function detachObra(Request $request, ArteExhibicion $exhibicion, ArteObra $obra)
    {
        $exhibicion->obras()->detach($obra->id);

        $enOtraExhibicion = ArteExhibicion::whereHas('obras', fn($q) => $q->where('arte_obras.id', $obra->id))->where('activa', true)->exists();
        if (!$enOtraExhibicion && $obra->estado === 'en_exhibicion') {
            $obra->update(['estado' => 'disponible']);
        }

        return back()->with('success', 'Obra removida de la exhibición.');
    }

    private function syncObras(ArteExhibicion $exhibicion, array $obraIds): void
    {
        $obraIds = array_values(array_unique(array_filter(array_map('intval', $obraIds))));

        $attached = collect($obraIds)->mapWithKeys(fn($id) => [
            $id => ['fecha_asignacion' => now()->toDateString()],
        ])->all();

        $exhibicion->obras()->sync($attached);

        foreach ($obraIds as $id) {
            ArteObra::where('id', $id)->where('estado', 'disponible')->update(['estado' => 'en_exhibicion']);
        }

        $oldIds = $exhibicion->obras()->pluck('arte_obras.id')->diff($obraIds);
        foreach ($oldIds as $id) {
            $obra = ArteObra::find($id);
            if ($obra && $obra->estado === 'en_exhibicion') {
                $enOtra = ArteExhibicion::whereHas('obras', fn($q) => $q->where('arte_obras.id', $id))->where('activa', true)->exists();
                if (!$enOtra) $obra->update(['estado' => 'disponible']);
            }
        }
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:300',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:300',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean',
            'obra_ids' => 'nullable|array',
            'obra_ids.*' => 'exists:arte_obras,id',
        ]);
    }
}