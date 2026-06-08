<?php

namespace App\Http\Controllers;

use App\Models\NcfSequence;
use Illuminate\Http\Request;

class NcfController extends Controller
{
    public function index()
    {
        $sequences = NcfSequence::orderBy('prefijo')->get();
        return view('ncf.index', compact('sequences'));
    }

    public function create()
    {
        return view('ncf.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'prefijo' => 'required|string|size:3',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|gt:desde',
            'actual' => 'required|integer|min:0',
            'fecha_vencimiento' => 'required|date|after:today',
        ]);

        NcfSequence::create($request->all());

        return redirect()->route('ncf.index')->with('success', 'Secuencia NCF creada correctamente.');
    }

    public function edit(NcfSequence $ncf)
    {
        return view('ncf.edit', compact('ncf'));
    }

    public function update(Request $request, NcfSequence $ncf)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'prefijo' => 'required|string|size:3',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|gt:desde',
            'actual' => 'required|integer|min:0',
            'fecha_vencimiento' => 'required|date',
        ]);

        $ncf->update($request->all());

        return redirect()->route('ncf.index')->with('success', 'Secuencia NCF actualizada correctamente.');
    }

    public function destroy(NcfSequence $ncf)
    {
        $ncf->delete();
        return redirect()->route('ncf.index')->with('success', 'Secuencia NCF eliminada.');
    }

    public function toggleStatus(NcfSequence $ncf)
    {
        $ncf->update(['activo' => !$ncf->activo]);
        return back()->with('success', 'Estado de la secuencia actualizado.');
    }
}
