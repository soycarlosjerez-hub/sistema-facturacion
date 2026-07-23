<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\OrdenReparacion;
use App\Models\ServicioDomotica;
use App\Models\Tecnico;
use App\Models\Garantia;
use App\Models\Producto;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardTecnologiaController extends Controller
{
    public function index(Request $request)
    {
        $periodStart = $this->getPeriodStartDate($request);

        $kpis = [
            'total_equipos' => Equipo::count(),
            'equipos_disponibles' => Equipo::where('estado', 'disponible')->count(),
            'ordenes_pendientes' => OrdenReparacion::whereIn('estado', ['recibido', 'pendiente'])->count(),
            'en_reparacion' => OrdenReparacion::where('estado', 'en_reparacion')->count(),
            'listos_para_entrega' => OrdenReparacion::where('estado', 'terminado')->count(),
            'servicios_domotica_activos' => ServicioDomotica::whereIn('estado', ['pendiente', 'programado', 'en_curso'])->count(),
            'garantias_vigentes' => Garantia::vigentes()->count(),
        ];

        $chartData = [
            'revenue_by_month' => $this->getRevenueByMonth($periodStart),
            'orders_by_status' => OrdenReparacion::selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->pluck('total', 'estado')
                ->toArray(),
            'top_brands' => Equipo::whereNotNull('marca')
                ->selectRaw('marca, COUNT(*) as count')
                ->groupBy('marca')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('count', 'marca')
                ->toArray(),
            'top_tecnicos' => Tecnico::activos()
                ->withCount(['ordenesReparacion' => function ($q) {
                    $q->where('estado', 'entregado');
                }])
                ->orderByDesc('ordenes_reparacion_count')
                ->limit(5)
                ->get(['id', 'nombre', 'ordenes_reparacion_count']),
        ];

        $recentOrders = OrdenReparacion::with(['cliente', 'equipo', 'tecnico'])
            ->latest()
            ->limit(10)
            ->get();

        $quickActions = [
            'nueva_orden' => route('tecnicas.create'),
            'registrar_equipo' => route('equipos.create'),
            'nuevo_servicio_domotica' => route('domotica.create'),
            'buscar_imei' => route('equipos.buscar-imai'),
        ];

        return view('tecnologia.dashboard', compact(
            'kpis',
            'chartData',
            'recentOrders',
            'quickActions'
        ));
    }

    public function getKpis()
    {
        $kpis = [
            'total_equipos' => Equipo::count(),
            'equipos_disponibles' => Equipo::where('estado', 'disponible')->count(),
            'ordenes_pendientes' => OrdenReparacion::whereIn('estado', ['recibido', 'pendiente'])->count(),
            'en_reparacion' => OrdenReparacion::where('estado', 'en_reparacion')->count(),
            'listos_para_entrega' => OrdenReparacion::where('estado', 'terminado')->count(),
            'servicios_domotica_activos' => ServicioDomotica::whereIn('estado', ['pendiente', 'programado', 'en_curso'])->count(),
            'garantias_vigentes' => Garantia::vigentes()->count(),
        ];

        return response()->json($kpis);
    }

    public function getRecentOrders()
    {
        $orders = OrdenReparacion::with(['cliente', 'equipo', 'tecnico'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($orden) {
                return [
                    'id' => $orden->id,
                    'numero_orden' => $orden->numero_orden,
                    'cliente' => $orden->cliente ? $orden->cliente->nombre : '-',
                    'equipo' => $orden->equipo ? "{$orden->equipo->marca} {$orden->equipo->modelo}" : '-',
                    'estado' => $orden->estado_label ?? $orden->estado,
                    'total' => number_format($orden->total ?? 0, 2),
                    'fecha_recibo' => $orden->fecha_recibo ? $orden->fecha_recibo->format('d/m/Y H:i') : '-',
                ];
            });

        return response()->json($orders);
    }

    private function getPeriodStartDate(Request $request): Carbon
    {
        $period = $request->get('period', '6months');

        return match ($period) {
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'quarter' => now()->startOfQuarter(),
            'year' => now()->startOfYear(),
            default => now()->subMonths(6)->startOfDay(),
        };
    }

    private function getRevenueByMonth(Carbon $startDate): array
    {
        $months = [];
        $current = clone $startDate;

        while ($current <= now()) {
            $key = $current->format('Y-m');
            $amount = OrdenReparacion::whereYear('created_at', $current->year)
                ->whereMonth('created_at', $current->month)
                ->where('estado', 'entregado')
                ->sum('total');

            $months[] = [
                'label' => $current->format('M Y'),
                'value' => round((float) $amount, 2),
            ];

            $current->addMonth();
        }

        return $months;
    }
}
