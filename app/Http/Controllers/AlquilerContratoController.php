<?php

namespace App\Http\Controllers;

use App\Models\AlquilerContrato;
use App\Models\AlquilerVivienda;
use App\Models\AlquilerInquilino;
use Illuminate\Http\Request;

class AlquilerContratoController extends Controller
{
    public function index()
    {
        $instanceId = auth()->user()->business_instance_id;
        $contratos = AlquilerContrato::porInstancia($instanceId)
            ->with('vivienda', 'inquilino')
            ->orderByDesc('created_at')
            ->get();
        return view('alquileres.contratos.index', compact('contratos'));
    }

    public function create()
    {
        $instanceId = auth()->user()->business_instance_id;
        $viviendas = AlquilerVivienda::porInstancia($instanceId)->where('activo', true)->get();
        $inquilinos = AlquilerInquilino::porInstancia($instanceId)->where('activo', true)->get();
        return view('alquileres.contratos.create', compact('viviendas', 'inquilinos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vivienda_id' => 'required|exists:alquileres_viviendas,id',
            'inquilino_id' => 'required|exists:alquileres_inquilinos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'monto_alquiler' => 'required|numeric|min:0',
            'monto_deposito' => 'nullable|numeric|min:0',
            'dia_pago' => 'required|integer|between:1,31',
            'deposito_pagado' => 'nullable|boolean',
            'notas' => 'nullable|string',
        ]);

        $instanceId = auth()->user()->business_instance_id;
        $data['business_instance_id'] = $instanceId;
        $data['monto_deposito'] = $data['monto_deposito'] ?? 0;
        $data['deposito_pagado'] = $request->boolean('deposito_pagado');
        $data['estado'] = 'activo';

        $contrato = AlquilerContrato::create($data);

        $contrato->vivienda->update(['estado' => 'alquilado']);

        return redirect()->route('alquileres.contratos.index')
            ->with('success', 'Contrato creado correctamente.');
    }

    public function edit($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $contrato = AlquilerContrato::porInstancia($instanceId)
            ->with('vivienda', 'inquilino')
            ->findOrFail($id);
        $viviendas = AlquilerVivienda::porInstancia($instanceId)->where('activo', true)->get();
        $inquilinos = AlquilerInquilino::porInstancia($instanceId)->where('activo', true)->get();
        return view('alquileres.contratos.edit', compact('contrato', 'viviendas', 'inquilinos'));
    }

    public function update(Request $request, $id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $contrato = AlquilerContrato::porInstancia($instanceId)->findOrFail($id);

        $data = $request->validate([
            'vivienda_id' => 'required|exists:alquileres_viviendas,id',
            'inquilino_id' => 'required|exists:alquileres_inquilinos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'monto_alquiler' => 'required|numeric|min:0',
            'monto_deposito' => 'nullable|numeric|min:0',
            'dia_pago' => 'required|integer|between:1,31',
            'estado' => 'required|string|in:activo,vencido,cancelado,finalizado',
            'deposito_pagado' => 'nullable|boolean',
            'notas' => 'nullable|string',
        ]);

        $data['monto_deposito'] = $data['monto_deposito'] ?? 0;
        $data['deposito_pagado'] = $request->boolean('deposito_pagado');

        $oldViviendaId = $contrato->vivienda_id;
        $contrato->update($data);

        if ($oldViviendaId != $data['vivienda_id']) {
            AlquilerVivienda::find($oldViviendaId)?->update(['estado' => 'disponible']);
            AlquilerVivienda::find($data['vivienda_id'])?->update(['estado' => 'alquilado']);
        }

        if ($data['estado'] !== 'activo') {
            $contrato->vivienda->update(['estado' => 'disponible']);
        }

        return redirect()->route('alquileres.contratos.index')
            ->with('success', 'Contrato actualizado correctamente.');
    }

    public function destroy($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $contrato = AlquilerContrato::porInstancia($instanceId)->findOrFail($id);

        $viviendaId = $contrato->vivienda_id;

        $contrato->delete();

        AlquilerVivienda::find($viviendaId)?->update(['estado' => 'disponible']);

        return redirect()->route('alquileres.contratos.index')
            ->with('success', 'Contrato eliminado correctamente.');
    }
}
