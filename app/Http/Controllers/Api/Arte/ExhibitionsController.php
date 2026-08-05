<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExhibicionResource;
use App\Models\Exhibicion;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExhibitionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Exhibicion::withCount('obras')
            ->when($request->activo !== null, fn ($q) => $q->where('activo', filter_var($request->activo, FILTER_VALIDATE_BOOLEAN)))
            ->when($request->tipo, fn ($q) => $q->where('tipo', $request->tipo))
            ->when($request->search, fn ($q) => $q->where(function ($inner) use ($request) {
                $inner->where('titulo', 'like', "%{$request->search}%")
                    ->orWhere('lugar', 'like', "%{$request->search}%");
            }))
            ->when($request->past_only, fn ($q) => $q->where('fecha_fin', '<', now()))
            ->when($request->future_only, fn ($q) => $q->where(function ($inner) {
                $inner->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now());
            }));

        $query->orderBy('fecha_inicio', 'desc');

        return ExhibicionResource::collection($query->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'lugar' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
            'tipo' => 'required|in:individual,colectiva',
            'activo' => 'nullable|boolean',
            'featured_image' => 'nullable|string',
        ]);

        $exhibicion = Exhibicion::create($validated);

        return new ExhibicionResource($exhibicion->loadCount('obras'));
    }

    public function show(Exhibicion $exhibicion)
    {
        return new ExhibicionResource($exhibicion->load('obras'));
    }

    public function update(Request $request, Exhibicion $exhibicion)
    {
        $validated = $request->validate([
            'titulo' => 'sometimes|string|max:255',
            'lugar' => 'sometimes|string|max:255',
            'fecha_inicio' => 'sometimes|nullable|date',
            'fecha_fin' => 'sometimes|nullable|date',
            'descripcion' => 'sometimes|nullable|string',
            'tipo' => 'sometimes|in:individual,colectiva',
            'activo' => 'sometimes|nullable|boolean',
            'featured_image' => 'sometimes|nullable|string',
        ]);

        $exhibicion->update($validated);

        return new ExhibicionResource($exhibicion->load('obras'));
    }

    public function destroy(Exhibicion $exhibicion)
    {
        $exhibicion->delete();
        return response()->json(['message' => 'Exhibicion eliminada correctamente.']);
    }

    public function assignObras(Request $request, Exhibicion $exhibicion)
    {
        $validated = $request->validate([
            'obra_ids' => 'required|array|min:1',
            'obra_ids.*' => 'required|integer|exists:obras,id',
        ]);

        $exhibicion->obras()->sync($validated['obra_ids']);

        return response()->json([
            'message' => 'Obras asignadas a la exhibicion correctamente.',
            'assigned_count' => count($validated['obra_ids']),
        ]);
    }

    public function removeObra(Exhibicion $exhibicion, Obra $obra)
    {
        $exhibicion->obras()->detach($obra->id);

        return response()->json(['message' => 'Obra removida de la exhibicion correctamente.']);
    }
}
