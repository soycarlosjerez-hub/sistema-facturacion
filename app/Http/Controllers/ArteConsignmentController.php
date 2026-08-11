<?php

namespace App\Http\Controllers;

use App\Models\ArteConsignment;
use App\Models\ArteObra;
use Illuminate\Http\Request;

class ArteConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ArteConsignment::with('obra.artista');

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        if ($q = $request->get('q')) {
            $query->where(fn($b) => $b
                ->where('consignante', 'like', "%{$q}%")
                ->orWhereHas('obra', fn($o) => $o->where('titulo', 'like', "%{$q}%")));
        }

        $consignaciones = $query->orderByDesc('fecha_inicio')->paginate(15)->withQueryString();
        return view('arte.consignaciones.index', compact('consignaciones'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['tenant_id'] = auth()->user()->business_instance_id ?? null;

        ArteConsignment::create($data);
        return redirect()->route('arte.consignaciones.index')->with('success', 'Consignación creada correctamente.');
    }

    public function edit(ArteConsignment $consignacion)
    {
        return view('arte.consignaciones.edit', [
            'consignacion' => $consignacion,
            'obras' => ArteObra::disponibles()->orderBy('titulo')->get(),
        ]);
    }

    public function update(Request $request, ArteConsignment $consignacion)
    {
        $consignacion->update($this->validar($request));
        return redirect()->route('arte.consignaciones.index')->with('success', 'Consignación actualizada correctamente.');
    }

    public function destroy(ArteConsignment $consignacion)
    {
        $consignacion->delete();
        return redirect()->route('arte.consignaciones.index')->with('success', 'Consignación eliminada.');
    }

    public function create()
    {
        $obras = ArteObra::disponibles()->orderBy('titulo')->get();
        return view('arte.consignaciones.create', compact('obras'));
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'obra_id' => 'required|exists:arte_obras,id',
            'consignante' => 'required|string|max:200',
            'porcentaje_comision' => 'nullable|numeric|min:0|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:activa,completada,cancelada',
            'monto_entregado' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);
    }
}