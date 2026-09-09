<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use App\Models\DeliveryTracking;
use App\Models\DriverEarning;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardDeliveryController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfDay();

        // ── Entregas hoy (fuente: ventas delivery) ──
        $ventasHoy = Venta::where('tipo_orden', 'delivery')
            ->whereDate('created_at', $today);

        $totalHoy = (clone $ventasHoy)->count();

        // Pendientes = ventas sin cobrar
        $pendientes = (clone $ventasHoy)
            ->whereIn('estado', ['pendiente', 'abierta'])
            ->count();

        // En camino = con driver asignado, aún no cobrada ni cancelada
        $enCamino = (clone $ventasHoy)
            ->whereNotNull('driver_id')
            ->whereNotIn('estado', ['cobrada', 'cancelada'])
            ->count();

        // Entregadas = estado cobrada (pagada y completada)
        $entregadas = (clone $ventasHoy)
            ->where('estado', 'cobrada')
            ->count();

        // Fallidas = tracking status fallido
        $fallidas = DeliveryTracking::where('status', 'fallido')
            ->whereDate('created_at', $today)
            ->count();

        // ── Drivers activos ──
        $totalDriversActivos = DeliveryDriver::activos()->count();

        // ── Ganancias del mes (corregido: periodo que se superpone con el mes) ──
        $gananciasMes = DriverEarning::where('periodo_inicio', '<=', $monthEnd)
            ->where('periodo_fin', '>=', $monthStart)
            ->sum('total_ganancias');

        // ── Top 5 drivers por entregas completadas este mes ──
        $topDrivers = Venta::where('tipo_orden', 'delivery')
            ->whereNotNull('driver_id')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('driver_id, COUNT(*) as total')
            ->groupBy('driver_id')
            ->with('driver:id,nombre,apellido')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // ── Gráfico entregas últimos 7 días ──
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = $date->copy()->addDay();
            $count = Venta::where('tipo_orden', 'delivery')
                ->where('created_at', '>=', $date)
                ->where('created_at', '<', $nextDate)
                ->count();
            $chartData[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d/M'),
                'count' => $count,
            ];
        }

        return view('dashboard.delivery', compact(
            'totalHoy',
            'pendientes',
            'enCamino',
            'entregadas',
            'fallidas',
            'totalDriversActivos',
            'gananciasMes',
            'topDrivers',
            'chartData'
        ));
    }
}
