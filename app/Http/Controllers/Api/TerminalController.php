<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TerminalResource;
use App\Models\Terminal;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function index()
    {
        return TerminalResource::collection(Terminal::with('caja')->orderBy('nombre')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'    => 'required|string|max:100',
            'codigo'    => 'required|string|max:50|unique:terminales,codigo',
            'ubicacion' => 'nullable|string|max:200',
            'caja_id'   => 'nullable|exists:cajas,id',
            'activo'    => 'boolean',
        ]);

        $validated['tenant_id'] = auth()->user()->business_instance_id;
        $terminal = Terminal::create($validated);

        return new TerminalResource($terminal->load('caja'));
    }

    public function show(Terminal $terminal)
    {
        return new TerminalResource($terminal->load('caja'));
    }

    public function update(Request $request, Terminal $terminal)
    {
        $validated = $request->validate([
            'nombre'    => 'sometimes|string|max:100',
            'codigo'    => 'sometimes|string|max:50|unique:terminales,codigo,' . $terminal->id,
            'ubicacion' => 'nullable|string|max:200',
            'caja_id'   => 'nullable|exists:cajas,id',
            'activo'    => 'boolean',
        ]);

        $terminal->update($validated);

        return new TerminalResource($terminal->fresh()->load('caja'));
    }

    public function destroy(Terminal $terminal)
    {
        $terminal->update(['activo' => false]);
        return response()->json(['message' => 'Terminal desactivada.']);
    }
}
