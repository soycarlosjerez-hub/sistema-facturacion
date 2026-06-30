<?php

namespace App\Http\Controllers;

use App\Models\AlquilerVivienda;
use App\Models\AlquilerInquilino;
use App\Models\AlquilerContrato;
use App\Models\AlquilerPago;
use Illuminate\Http\Request;

class AlquilerController extends Controller
{
    public function index()
    {
        $instanceId = auth()->user()->business_instance_id;

        $viviendas = AlquilerVivienda::porInstancia($instanceId)->get();
        $inquilinos = AlquilerInquilino::porInstancia($instanceId)->get();
        $contratos = AlquilerContrato::porInstancia($instanceId)->get();
        $pagos = AlquilerPago::porInstancia($instanceId)->get();

        $stats = [
            'total_viviendas' => $viviendas->count(),
            'disponibles' => $viviendas->where('estado', 'disponible')->count(),
            'alquiladas' => $viviendas->where('estado', 'alquilado')->count(),
            'contratos_activos' => $contratos->where('estado', 'activo')->count(),
            'inquilinos_activos' => $inquilinos->where('activo', true)->count(),
            'pagos_mes' => $pagos->where('mes_cobrado', now()->month)
                ->where('ano_cobrado', now()->year)->sum('monto'),
            'pagos_pendientes' => $contratos->where('estado', 'activo')->count(),
        ];

        $proximosVencimientos = AlquilerContrato::porInstancia($instanceId)
            ->where('estado', 'activo')
            ->where('fecha_fin', '<=', now()->addDays(30))
            ->with('vivienda', 'inquilino')
            ->get();

        return view('alquileres.index', compact('stats', 'proximosVencimientos'));
    }
}
