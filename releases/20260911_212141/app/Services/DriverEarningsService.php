<?php

namespace App\Services;

use App\Models\DeliveryDriver;
use App\Models\DriverEarning;
use App\Models\DriverEarningDetail;
use App\Models\Orden;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverEarningsService
{
    public function calcularPeriodoGanancias($driverId, $desde, $hasta)
    {
        $driver = DeliveryDriver::findOrFail($driverId);

        $entregas = Orden::where('driver_id', $driverId)
            ->where('tracking_status', 'entregado')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->get();

        $ventasDelivery = Venta::where('driver_id', $driverId)
            ->where('tipo_orden', 'delivery')
            ->where('estado', 'cobrada')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->whereNull('id')
            ->get();

        $ventasIds = $entregas->pluck('venta_id')->filter()->toArray();
        $ventasDelivery = $ventasDelivery->whereNotIn('id', $ventasIds);

        $allItems = $entregas->concat($ventasDelivery);

        $totalGanancias = 0;
        $totalPropinas = 0;
        $detalles = [];

        foreach ($allItems as $item) {
            $ganancia = $item->delivery_fee ?? 0;
            $propina = $item->propina ?? 0;
            $total = $ganancia + $propina;

            $totalGanancias += $ganancia;
            $totalPropinas += $propina;

            $detalles[] = [
                'orden_id' => $item instanceof Orden ? $item->id : null,
                'venta_id' => $item instanceof Venta ? $item->id : ($item->venta_id ?? null),
                'fecha' => $item->created_at,
                'monto_ganancia' => $ganancia,
                'propina' => $propina,
                'total' => $total,
                'cliente' => $item->cliente?->nombre ?? 'N/A',
                'direccion' => $item->direccion_entrega ?? $item->delivery_address ?? 'N/A',
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

            $entregas = Orden::where('driver_id', $driverId)
                ->where('tracking_status', 'entregado')
                ->whereBetween(DB::raw('DATE(created_at)'), [$periodoInicio, $periodoFin])
                ->get();

            $ventasIds = $entregas->pluck('venta_id')->filter()->toArray();

            $ventasDelivery = Venta::where('driver_id', $driverId)
                ->where('tipo_orden', 'delivery')
                ->where('estado', 'cobrada')
                ->whereBetween(DB::raw('DATE(created_at)'), [$periodoInicio, $periodoFin])
                ->whereNotIn('id', $ventasIds)
                ->get();

            $allItems = $entregas->concat($ventasDelivery);

            $earning = DriverEarning::create([
                'tenant_id' => $tenantId,
                'driver_id' => $driverId,
                'periodo_inicio' => $periodoInicio,
                'periodo_fin' => $periodoFin,
                'total_entregas' => count($allItems),
                'total_ganancias' => 0,
            ]);

            $totalGanancias = 0;

            foreach ($allItems as $item) {
                $ganancia = $item->delivery_fee ?? 0;
                $propina = $item->propina ?? 0;
                $total = $ganancia + $propina;

                DriverEarningDetail::create([
                    'tenant_id' => $tenantId,
                    'driver_earning_id' => $earning->id,
                    'orden_id' => $item instanceof Orden ? $item->id : null,
                    'venta_id' => $item instanceof Venta ? $item->id : ($item->venta_id ?? null),
                    'monto_ganancia' => $ganancia,
                    'propina' => $propina,
                    'fecha' => $item->created_at,
                ]);

                $totalGanancias += $total;
            }

            $earning->update(['total_ganancias' => round($totalGanancias, 2)]);

            DB::commit();

            return [
                'success' => true,
                'earning_id' => $earning->id,
                'total_ganancias' => round($totalGanancias, 2),
                'total_entregas' => count($allItems),
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function generarReporteGanancias($driverId, $desde, $hasta)
    {
        $driver = DeliveryDriver::findOrFail($driverId);

        $entregas = Orden::where('driver_id', $driverId)
            ->where('tracking_status', 'entregado')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->get();

        $ventasIds = $entregas->pluck('venta_id')->filter()->toArray();

        $ventasDelivery = Venta::where('driver_id', $driverId)
            ->where('tipo_orden', 'delivery')
            ->where('estado', 'cobrada')
            ->whereBetween(DB::raw('DATE(created_at)'), [$desde, $hasta])
            ->whereNotIn('id', $ventasIds)
            ->get();

        $allItems = $entregas->concat($ventasDelivery)->sortBy('created_at');

        $filaCabecera = [
            'Reporte de Ganancias',
            'Driver: '.$driver->nombreCompleto,
            'Desde: '.$desde,
            'Hasta: '.$hasta,
        ];

        $filas = [['Fecha', 'Orden/Venta', 'Cliente', 'Direccion', 'Monto Base', 'Propina', 'Total']];

        $totalBase = 0;
        $totalPropinas = 0;
        $totalGeneral = 0;

        foreach ($allItems as $item) {
            $ganancia = $item->delivery_fee ?? 0;
            $propina = $item->propina ?? 0;
            $total = $ganancia + $propina;

            $totalBase += $ganancia;
            $totalPropinas += $propina;
            $totalGeneral += $total;

            $ref = $item instanceof Orden ? '#O-'.$item->id : '#V-'.$item->id;

            $filas[] = [
                $item->created_at->format('Y-m-d H:i'),
                $ref,
                $item->cliente?->nombre ?? 'N/A',
                '"'.($item->direccion_entrega ?? $item->delivery_address ?? 'N/A').'"',
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
                'total_entregas' => count($allItems),
                'total_base' => round($totalBase, 2),
                'total_propinas' => round($totalPropinas, 2),
                'total_general' => round($totalGeneral, 2),
            ],
        ];
    }
}
