<?php

namespace App\Services;

use App\Models\DeliveryDriver;
use App\Models\DeliveryTracking;
use App\Models\DriverEarning;
use App\Models\DriverEarningDetail;
use App\Models\Orden;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverEarningsService
{
    public function calcularPeriodoGanancias($driverId, $desde, $hasta)
    {
        $driver = DeliveryDriver::findOrFail($driverId);

        $entregas = Orden::where('entrega_empresa_id', $driverId)
            ->where('tracking_status', 'entregado')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->get();

        $totalGanancias = 0;
        $totalPropinas = 0;
        $detalles = [];

        foreach ($entregas as $orden) {
            $ganancia = $orden->delivery_fee ?? 0;
            $propina = $orden->propina ?? 0;
            $total = $ganancia + $propina;

            $totalGanancias += $ganancia;
            $totalPropinas += $propina;

            $detalles[] = [
                'orden_id' => $orden->id,
                'fecha' => $orden->created_at,
                'monto_ganancia' => $ganancia,
                'propina' => $propina,
                'total' => $total,
                'cliente' => $orden->cliente?->nombre ?? 'N/A',
                'direccion' => $orden->direccion_entrega ?? 'N/A',
            ];
        }

        return [
            'total_ganancias' => round($totalGanancias, 2),
            'total_propinas' => round($totalPropinas, 2),
            'total_ingresos' => round($totalGanancias + $totalPropinas, 2),
            'total_entregas' => count($detalles),
            'driver' => [
                'id' => $driver->id,
                'nombre' => $driver->nombreCompleto,
            ],
            'desde' => $desde,
            'hasta' => $hasta,
            'detalles' => $detalles,
        ];
    }

    public function distribuirGanancias($driverId, $periodoInicio, $periodoFin)
    {
        $driver = DeliveryDriver::findOrFail($driverId);
        $tenantId = Auth::user()->business_instance_id;

        DB::beginTransaction();
        try {
            $existingEarning = DriverEarning::where('driver_id', $driverId)
                ->where('periodo_inicio', $periodoInicio)
                ->where('periodo_fin', $periodoFin)
                ->first();

            if ($existingEarning) {
                $existingEarning->details()->delete();
                $existingEarning->delete();
            }

            $entregas = Orden::where('entrega_empresa_id', $driverId)
                ->where('tracking_status', 'entregado')
                ->whereBetween(DB::raw('DATE(created_at)'), [$periodoInicio, $periodoFin])
                ->get();

            $earning = DriverEarning::create([
                'tenant_id' => $tenantId,
                'driver_id' => $driverId,
                'periodo_inicio' => $periodoInicio,
                'periodo_fin' => $periodoFin,
                'total_entregas' => count($entregas),
                'total_ganancias' => 0,
            ]);

            $totalGanancias = 0;

            foreach ($entregas as $orden) {
                $ganancia = $orden->delivery_fee ?? 0;
                $propina = $orden->propina ?? 0;
                $total = $ganancia + $propina;

                DriverEarningDetail::create([
                    'tenant_id' => $tenantId,
                    'driver_earning_id' => $earning->id,
                    'orden_id' => $orden->id,
                    'venta_id' => $orden->id,
                    'monto_ganancia' => $ganancia,
                    'propina' => $propina,
                    'fecha' => $orden->created_at,
                ]);

                $totalGanancias += $total;
            }

            $earning->update(['total_ganancias' => round($totalGanancias, 2)]);

            DB::commit();

            return [
                'success' => true,
                'earning_id' => $earning->id,
                'total_ganancias' => round($totalGanancias, 2),
                'total_entregas' => count($entregas),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function generarReporteGanancias($driverId, $desde, $hasta)
    {
        $driver = DeliveryDriver::findOrFail($driverId);

        $entregas = Orden::where('entrega_empresa_id', $driverId)
            ->where('tracking_status', 'entregado')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->orderBy('created_at', 'desc')
            ->get();

        $filaCabecera = [
            'Reporte de Ganancias',
            'Driver: ' . $driver->nombreCompleto,
            'Desde: ' . $desde,
            'Hasta: ' . $hasta,
        ];

        $filas = [['Fecha', 'Orden', 'Cliente', 'Direccion', 'Monto Base', 'Propina', 'Total']];

        $totalBase = 0;
        $totalPropinas = 0;
        $totalGeneral = 0;

        foreach ($entregas as $orden) {
            $ganancia = $orden->delivery_fee ?? 0;
            $propina = $orden->propina ?? 0;
            $total = $ganancia + $propina;

            $totalBase += $ganancia;
            $totalPropinas += $propina;
            $totalGeneral += $total;

            $filas[] = [
                $orden->created_at->format('Y-m-d H:i'),
                '#' . $orden->id,
                $orden->cliente?->nombre ?? 'N/A',
                '"' . ($orden->direccion_entrega ?? 'N/A') . '"',
                number_format($ganancia, 2),
                number_format($propina, 2),
                number_format($total, 2),
            ];
        }

        $filasTotales = [[
            '', '', '',
            'TOTALES:',
            number_format($totalBase, 2),
            number_format($totalPropinas, 2),
            number_format($totalGeneral, 2),
        ]];

        return [
            'cabecera' => $filaCabecera,
            'filas' => array_merge($filas, $filasTotales),
            'resumen' => [
                'total_entregas' => count($entregas),
                'total_base' => round($totalBase, 2),
                'total_propinas' => round($totalPropinas, 2),
                'total_general' => round($totalGeneral, 2),
            ],
        ];
    }
}
