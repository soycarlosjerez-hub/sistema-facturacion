<?php

namespace App\Http\Controllers;

use App\Models\SecuenciaEcf;
use Illuminate\Http\Request;

class SecuenciaEcfController extends Controller
{
    public function index()
    {
        $secuencias = SecuenciaEcf::orderBy('tipo_ecf')->get();
        $stats = [
            'total' => $secuencias->count(),
            'activas' => $secuencias->where('activo', true)->count(),
            'vencidas' => $secuencias->filter(fn($s) => $s->vencida())->count(),
            'agotadas' => $secuencias->filter(fn($s) => $s->agotada())->count(),
        ];
        return view('secuencias-ecf.index', compact('secuencias', 'stats'));
    }

    public function create()
    {
        $tipos = SecuenciaEcf::TIPOS;
        $usadas = SecuenciaEcf::pluck('tipo_ecf')->toArray();
        return view('secuencias-ecf.create', compact('tipos', 'usadas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_ecf' => 'required|string|size:3|unique:secuencias_ecf,tipo_ecf',
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|gt:desde',
            'actual' => 'required|integer|min:0',
            'fecha_vencimiento' => 'required|date|after:today',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'tipo_ecf.size' => 'El tipo debe ser exactamente 3 caracteres (ej: E31, E32).',
            'tipo_ecf.unique' => 'Ya existe una secuencia registrada para este tipo de e-CF.',
            'hasta.gt' => 'El campo "hasta" debe ser mayor que "desde".',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        SecuenciaEcf::create($data);

        return redirect()->route('secuencias-ecf.index')
            ->with('success', "Secuencia e-CF {$data['tipo_ecf']} creada correctamente.");
    }

    public function edit(SecuenciaEcf $secuencias_ecf)
    {
        $secuencia = $secuencias_ecf;
        $tipos = SecuenciaEcf::TIPOS;
        return view('secuencias-ecf.edit', compact('secuencia', 'tipos'));
    }

    public function update(Request $request, SecuenciaEcf $secuencias_ecf)
    {
        $secuencia = $secuencias_ecf;
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo_ecf' => 'required|string|size:3|unique:secuencias_ecf,tipo_ecf,' . $secuencia->id,
            'desde' => 'required|integer|min:1',
            'hasta' => 'required|integer|gt:desde',
            'actual' => 'required|integer|min:0',
            'fecha_vencimiento' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $secuencia->update($data);

        return redirect()->route('secuencias-ecf.index')
            ->with('success', "Secuencia {$secuencia->tipo_ecf} actualizada.");
    }

    public function destroy(SecuenciaEcf $secuencias_ecf)
    {
        if ($secuencias_ecf->documentos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la secuencia tiene documentos emitidos.');
        }
        $tipo = $secuencias_ecf->tipo_ecf;
        $secuencias_ecf->delete();
        return back()->with('success', "Secuencia {$tipo} eliminada.");
    }

    public function toggle(SecuenciaEcf $secuencias_ecf)
    {
        $secuencias_ecf->update(['activo' => !$secuencias_ecf->activo]);
        return back()->with('success', 'Estado actualizado.');
    }
}
