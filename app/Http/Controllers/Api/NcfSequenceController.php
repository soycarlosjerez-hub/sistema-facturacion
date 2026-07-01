<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NcfSequenceResource;
use App\Models\NcfSequence;
use Illuminate\Http\Request;

class NcfSequenceController extends Controller
{
    public function index(Request $request)
    {
        $query = NcfSequence::query()
            ->when($request->sucursal_id, fn ($q) => $q->where('sucursal_id', $request->sucursal_id))
            ->when($request->tipo_ncf, fn ($q) => $q->where('tipo_ncf', $request->tipo_ncf))
            ->when($request->activa, fn ($q) => $q->where('activa', true));

        return NcfSequenceResource::collection($query->orderBy('tipo_ncf')->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'tipo_ncf' => 'required|string|max:50',
            'inicio' => 'required|string|max:50',
            'fin' => 'required|string|max:50',
            'activa' => 'boolean',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $sequence = NcfSequence::create($validated);

        return new NcfSequenceResource($sequence);
    }

    public function show(NcfSequence $ncfSequence)
    {
        return new NcfSequenceResource($ncfSequence);
    }

    public function update(Request $request, NcfSequence $ncfSequence)
    {
        $validated = $request->validate([
            'sucursal_id' => 'sometimes|exists:sucursales,id',
            'tipo_ncf' => 'sometimes|string|max:50',
            'inicio' => 'sometimes|string|max:50',
            'fin' => 'sometimes|string|max:50',
            'activa' => 'boolean',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        $ncfSequence->update($validated);

        return new NcfSequenceResource($ncfSequence);
    }

    public function destroy(NcfSequence $ncfSequence)
    {
        $ncfSequence->delete();
        return response()->json(['message' => 'Secuencia NCF eliminada.']);
    }
}
