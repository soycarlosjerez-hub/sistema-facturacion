<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTracking;
use App\Models\DeliveryDriver;
use App\Models\DriverEarning;
use App\Models\DriverEarningDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardDeliveryController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfDay();
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();

        // Entregas por estado (hoy)
        $deliveriesToday = DeliveryTracking::whereDate('created_at', $today)->get();
        $totalHoy = $deliveriesToday->count();
        $pendientes = $deliveriesToday->where('status', DeliveryTracking::STATUS_CREADO)->count();
        $enCamino = $deliveriesToday->where('status', DeliveryTracking::STATUS_EN_CAMINO)->count();
        $entregadas = $deliveriesToday->where('status', DeliveryTracking::STATUS_ENTREGADO)->count();
        $fallidas = $deliveriesToday->where('status', DeliveryTracking::STATUS_FALLIDO)->count();

        // Drivers activos
        $totalDriversActivos = DeliveryDriver::activos()->count();

        // Ganancias del mes
        $gananciasMes = DriverEarning::whereBetween('periodo_inicio', [$monthStart, $monthEnd])
            ->orWhereBetween('periodo_fin', [$monthStart, $monthEnd])
            ->sum('total_ganancias');

        // Top 5 drivers por entregas completadas (este mes)
        $topDrivers = DeliveryTracking::where('status', DeliveryTracking::STATUS_ENTREGADO)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->whereNotNull('driver_id')
            ->selectRaw('driver_id, COUNT(*) as total')
            ->groupBy('driver_id')
            ->with('driver:id,nombre,apellido')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Gráfico entregas últimos 7 días
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $nextDate = $date->copy()->addDay();
            $count = DeliveryTracking::where('created_at', '>=', $date)
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
