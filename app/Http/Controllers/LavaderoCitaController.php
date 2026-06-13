<?php

namespace App\Http\Controllers;

use App\Models\LavaderoCita;
use App\Models\Cliente;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class LavaderoCitaController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->get('fecha', today()->format('Y-m-d'));
        $citas = LavaderoCita::with('cliente', 'vehiculo', 'user')
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();
        return view('lavadero.citas.index', compact('citas', 'fecha'));
    }

    public function hoy()
    {
        $citas = LavaderoCita::with('cliente', 'vehiculo', 'user')
            ->delDia()
            ->orderBy('fecha_hora')
            ->get();
        return response()->json($citas);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_hora' => 'required|date',
            'servicio' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['sucursal_id'] = session('sucursal_id');

        LavaderoCita::create($data);
        return redirect()->route('lavadero.citas.index')->with('success', 'Cita creada');
    }

    public function update(Request $request, LavaderoCita $cita)
    {
        $data = $request->validate([
            'estado' => 'required|in:pendiente,confirmada,en_proceso,completada,cancelada',
            'fecha_hora' => 'sometimes|date',
            'servicio' => 'nullable|string|max:200',
            'notas' => 'nullable|string',
        ]);

        $cita->update($data);
        return redirect()->route('lavadero.citas.index')->with('success', 'Cita actualizada');
    }

    public function destroy(LavaderoCita $cita)
    {
        $cita->delete();
        return redirect()->route('lavadero.citas.index')->with('success', 'Cita eliminada');
    }
}
