<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Venta;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehiculo::with('cliente');
        if ($q = $request->get('q')) {
            $query->where(function ($qry) use ($q) {
                $qry->where('placa', 'like', "%{$q}%")
                    ->orWhere('marca', 'like', "%{$q}%")
                    ->orWhere('modelo', 'like', "%{$q}%")
                    ->orWhereHas('cliente', fn($c) => $c->where('nombre', 'like', "%{$q}%"));
            });
        }
        $vehiculos = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('lavadero.vehiculos.index', compact('vehiculos'));
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load('cliente');
        $ventas = Venta::with('detalles', 'pagos')
            ->where('vehiculo_id', $vehiculo->id)
            ->where('estado', '!=', 'abierta')
            ->orderByDesc('created_at')
            ->paginate(20, '*', 'historial_page');
        return view('lavadero.vehiculos.show', compact('vehiculo', 'ventas'));
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $data = $request->validate([
            'placa' => 'nullable|string|max:20',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'anio' => 'nullable|integer|min:1900|max:2099',
            'color' => 'nullable|string|max:50',
            'notas' => 'nullable|string',
        ]);

        $vehiculo->update($data);
        return redirect()->route('lavadero.vehiculos.index')->with('success', 'Vehículo actualizado');
    }
}
