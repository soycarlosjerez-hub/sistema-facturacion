<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use App\Models\DriverEarning;
use App\Models\DriverEarningDetail;
use App\Models\DeliveryTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverEarningsController extends Controller
{
    public function index(Request $request)
    {
        $query = DriverEarning::query()->with(['driver']);

        if ($driverId = $request->input('driver_id')) {
            $query->where('driver_id', $driverId);
        }

        if ($startDate = $request->input('periodo_inicio')) {
            $query->where('periodo_inicio', '>=', $startDate);
        }

        if ($endDate = $request->input('periodo_fin')) {
            $query->where('periodo_fin', '<=', $endDate);
        }

        $earnings = $query->latest()->paginate(15)->withQueryString();

        $drivers = DeliveryDriver::activos()->orderBy('nombre')->get(['id', 'nombre', 'apellido']);

        return view('driver-earnings.index', compact('earnings', 'drivers'));
    }

    public function show($id)
    {
        $earning = DriverEarning::with(['driver', 'details.orden', 'details.venta'])->findOrFail($id);

        return view('driver-earnings.show', compact('earning'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:delivery_drivers,id',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
        ]);

        $data['tenant_id'] = Auth::user()->business_instance_id;

        DriverEarning::create($data);

        return redirect()->route('driver-earnings.index')
            ->with('success', 'Período de ganancias creado correctamente.');
    }

    public function calcularGanancias(Request $request)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:delivery_drivers,id',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
        ]);

        $driverId = $data['driver_id'];
        $inicio = $data['periodo_inicio'];
        $fin = $data['periodo_fin'];

        // Obtener entregas completadas en el período
        $trackings = DeliveryTracking::where('driver_id', $driverId)
            ->where('status', DeliveryTracking::STATUS_ENTREGADO)
            ->whereBetween('created_at', [$inicio, $fin . ' 23:59:59'])
            ->with(['orden.pagos'])
            ->get();

        $totalGanancias = 0;
        $totalEntregas = $trackings->count();
        $detalles = [];

        foreach ($trackings as $tracking) {
            // Calcular ganancia: tarifa_delivery + propina si existe
            $ganancia = 0;
            $propina = 0;

            if ($tracking->orden) {
                $ganancia = floatval($tracking->orden->delivery_fee ?? 0);
                $propina = floatval($tracking->orden->propina ?? 0);
            } elseif ($tracking->venta) {
                $ganancia = floatval($tracking->venta->delivery_fee ?? 0);
                $propina = floatval($tracking->venta->propina ?? 0);
            }

            $totalGanancias += $ganancia + $propina;

            $detalles[] = [
                'tracking_id' => $tracking->id,
                'orden_id' => $tracking->orden_id,
                'venta_id' => $tracking->venta_id,
                'monto_ganancia' => round($ganancia, 2),
                'propina' => round($propina, 2),
                'fecha' => $tracking->created_at?->format('Y-m-d H:i:s'),
            ];
        }

        // Guardar o actualizar el registro de ganancias
        $earning = DriverEarning::firstOrCreate(
            [
                'driver_id' => $driverId,
                'periodo_inicio' => $inicio,
                'periodo_fin' => $fin,
            ],
            [
                'tenant_id' => Auth::user()->business_instance_id,
            ]
        );

        $earning->update([
            'total_entregas' => $totalEntregas,
            'total_ganancias' => round($totalGanancias, 2),
        ]);

        // Limpiar y recrear detalles
        DriverEarningDetail::where('driver_earning_id', $earning->id)->delete();

        foreach ($detalles as $detalle) {
            DriverEarningDetail::create(array_merge($detalle, [
                'tenant_id' => Auth::user()->business_instance_id,
                'driver_earning_id' => $earning->id,
            ]));
        }

        return response()->json([
            'success' => true,
            'message' => 'Ganancias calculadas correctamente.',
            'data' => [
                'driver_id' => $driverId,
                'periodo_inicio' => $inicio,
                'periodo_fin' => $fin,
                'total_entregas' => $totalEntregas,
                'total_ganancias' => round($totalGanancias, 2),
                'detalles' => $detalles,
            ],
        ]);
    }

    public function exportCsv(Request $request)
    {
        $data = $request->validate([
            'driver_id' => 'nullable|exists:delivery_drivers,id',
            'periodo_inicio' => 'required|date',
            'periodo_fin' => 'required|date|after_or_equal:periodo_inicio',
        ]);

        $query = DriverEarningDetail::query()->with(['orden', 'venta', 'earning.driver']);

        if ($driverId = $data['driver_id']) {
            $query->whereHas('earning', fn($q) => $q->where('driver_id', $driverId));
        }

        $query->whereHas('earning', fn($q) => $q
            ->where('periodo_inicio', '>=', $data['periodo_inicio'])
            ->where('periodo_fin', '<=', $data['periodo_fin'])
        );

        $details = $query->get();

        $response = new StreamedResponse(function () use ($details) {
            $handle = fopen('php://output', 'w');
            stream_set_encoding($handle, 'UTF-8');

            // BOM para Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($handle, [
                'ID Detalle',
                'Driver',
                'Fecha',
                'Orden',
                'Venta',
                'Ganancia',
                'Propina',
                'Total',
            ]);

            // Datos
            foreach ($details as $detail) {
                $driverName = $detail->earning?->driver ? $detail->earning->driver->nombre_completo : 'N/A';
                $ordenRef = $detail->orden ? $detail->orden->ncf : ($detail->venta ? $detail->venta->ncf : 'N/A');
                $total = round(floatval($detail->monto_ganancia) + floatval($detail->propina), 2);

                fputcsv($handle, [
                    $detail->id,
                    $driverName,
                    $detail->fecha?->format('Y-m-d H:i:s') ?? '',
                    $ordenRef,
                    $detail->venta_id ?? '',
                    number_format($detail->monto_ganancia, 2, '.', ''),
                    number_format($detail->propina, 2, '.', ''),
                    number_format($total, 2, '.', ''),
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="ganancias_drivers_' . date('Y-m-d') . '.csv"');

        return $response;
    }
}
