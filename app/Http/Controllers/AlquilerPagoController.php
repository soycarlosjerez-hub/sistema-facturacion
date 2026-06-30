<?php

namespace App\Http\Controllers;

use App\Models\AlquilerPago;
use App\Models\AlquilerContrato;
use Illuminate\Http\Request;

class AlquilerPagoController extends Controller
{
    public function index()
    {
        $instanceId = auth()->user()->business_instance_id;
        $pagos = AlquilerPago::porInstancia($instanceId)
            ->with('contrato.vivienda', 'contrato.inquilino')
            ->orderByDesc('fecha_pago')
            ->get();
        return view('alquileres.pagos.index', compact('pagos'));
    }

    public function create()
    {
        $instanceId = auth()->user()->business_instance_id;
        $contratos = AlquilerContrato::porInstancia($instanceId)
            ->where('estado', 'activo')
            ->with('vivienda', 'inquilino')
            ->get();
        return view('alquileres.pagos.create', compact('contratos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contrato_id' => 'required|exists:alquileres_contratos,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'mes_cobrado' => 'required|integer|between:1,12',
            'ano_cobrado' => 'required|integer|min:2020|max:2100',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,deposito,otro',
            'recibo_numero' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $data['business_instance_id'] = auth()->user()->business_instance_id;
        $data['registrado_por'] = auth()->id();

        AlquilerPago::create($data);

        return redirect()->route('alquileres.pagos.index')
            ->with('success', 'Pago registrado correctamente.');
    }

    public function edit($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $pago = AlquilerPago::porInstancia($instanceId)->with('contrato.vivienda', 'contrato.inquilino')->findOrFail($id);
        $contratos = AlquilerContrato::porInstancia($instanceId)->where('estado', 'activo')->with('vivienda', 'inquilino')->get();
        return view('alquileres.pagos.edit', compact('pago', 'contratos'));
    }

    public function update(Request $request, $id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $pago = AlquilerPago::porInstancia($instanceId)->findOrFail($id);

        $data = $request->validate([
            'contrato_id' => 'required|exists:alquileres_contratos,id',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'mes_cobrado' => 'required|integer|between:1,12',
            'ano_cobrado' => 'required|integer|min:2020|max:2100',
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,deposito,otro',
            'recibo_numero' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $pago->update($data);

        return redirect()->route('alquileres.pagos.index')
            ->with('success', 'Pago actualizado correctamente.');
    }

    public function destroy($id)
    {
        $instanceId = auth()->user()->business_instance_id;
        $pago = AlquilerPago::porInstancia($instanceId)->findOrFail($id);
        $pago->delete();

        return redirect()->route('alquileres.pagos.index')
            ->with('success', 'Pago eliminado correctamente.');
    }
}
