<?php

namespace App\Http\Controllers\Api\Arte;

use App\Http\Controllers\Controller;
use App\Models\Consignacion;
use App\Models\Encargo;
use App\Models\Obra;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function salesSummary(Request $request)
    {
        $period = $request->period ?? 'month';
        $startDate = now()->copy();
        $endDate = now();

        switch ($period) {
            case 'week':
                $startDate->startOfWeek();
                break;
            case 'month':
                $startDate->startOfMonth();
                break;
            case 'quarter':
                $startDate->startOfQuarter();
                break;
            case 'year':
                $startDate->startOfYear();
                break;
            case 'custom':
                if ($request->start_date) {
                    $startDate = \Carbon\Carbon::parse($request->start_date);
                }
                if ($request->end_date) {
                    $endDate = \Carbon\Carbon::parse($request->end_date);
                }
                break;
        }

        $vendidas = Obra::where('status', 'vendido')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $totalIngresos = Obra::where('status', 'vendido')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('cost_materials');

        $obrasDisponibles = Obra::where('status', 'disponible')->count();
        $encargosActivos = Encargo::whereNotIn('status', ['completado', 'cancelado'])->count();
        $valorEncargos = Encargo::whereNotIn('status', ['completado', 'cancelado'])->sum('saldo');
        $consignacionesActivas = Consignacion::where('estado', 'activa')->count();

        $topMediums = Obra::where('status', 'vendido')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select('medium', \DB::raw('COUNT(*) as count'))
            ->groupBy('medium')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'medium' => $item->medium,
                    'count' => $item->count,
                    'total_sales' => 0,
                ];
            });

        $mesAnterior = now()->copy()->subMonth();
        $ventasMesAnterior = Obra::where('status', 'vendido')
            ->whereMonth('updated_at', $mesAnterior->month)
            ->whereYear('updated_at', $mesAnterior->year)
            ->count();

        $variacion = $ventasMesAnterior > 0
            ? (($vendidas - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : 0;

        return response()->json([
            'data' => [
                'total_ventas' => $vendidas,
                'total_ingresos' => round($totalIngresos, 2),
                'promedio_venta' => $vendidas > 0 ? round($totalIngresos / $vendidas, 2) : 0,
                'obras_disponibles' => $obrasDisponibles,
                'obras_vendidas_mes' => $vendidas,
                'encargos_activos' => $encargosActivos,
                'valor_encargos_pendientes' => round($valorEncargos, 2),
                'consignaciones_activas' => $consignacionesActivas,
                'top_mediums' => $topMediums,
                'estadisticas_periodo' => [
                    'mes_actual' => [
                        'ventas' => $vendidas,
                        'ingresos' => round($totalIngresos, 2),
                    ],
                    'mes_anterior' => [
                        'ventas' => $ventasMesAnterior,
                        'ingresos' => 0,
                    ],
                    'variacion_porcentual' => round($variacion, 1),
                ],
            ],
        ]);
    }

    public function catalogStats()
    {
        $totalObras = Obra::count();
        $porStatus = Obra::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $porMedium = Obra::selectRaw('medium, COUNT(*) as count')
            ->groupBy('medium')
            ->pluck('count', 'medium')
            ->toArray();

        $obrasConCertificado = Obra::whereNotNull('certificate_number')->count();
        $obrasOriginales = Obra::where('is_original', true)->count();
        $ediciones = Obra::where('is_original', false)->count();
        $valorInventario = Obra::where('status', 'disponible')->sum('cost_materials');

        return response()->json([
            'data' => [
                'total_obras' => $totalObras,
                'por_status' => $porStatus,
                'por_medium' => $porMedium,
                'obras_con_certificado' => $obrasConCertificado,
                'obras_originales' => $obrasOriginales,
                'ediciones' => $ediciones,
                'valor_inventario' => round($valorInventario, 2),
            ],
        ]);
    }
}
