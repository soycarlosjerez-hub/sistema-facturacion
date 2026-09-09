<?php

namespace App\Http\Controllers;

use App\Models\DeliveryDriver;
use App\Models\DriverEarning;
use App\Models\DriverEarningDetail;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriverEarningsController extends Controller
{
    public function index(Request $request)
    {
        $drivers = DeliveryDriver::activos()->orderBy('nombre')->get(['id', 'nombre', 'apellido', 'telefono']);

        // Stats calculados en tiempo real desde ventas delivery
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfDay();

        $stats = Venta::where('tipo_orden', 'delivery')
            ->where('estado', 'completada')
            ->whereNotNull('driver_id')
            ->selectRaw('driver_id, COUNT(*) as entregas, COALESCE(SUM(delivery_fee), 0) as ganancias, COALESCE(SUM(propina), 0) as propinas')
            ->groupBy('driver_id')
            ->get();

        $totalGanancias = $stats->sum('ganancias');
        $totalPropinas = $stats->sum('propinas');
        $totalEntregas = $stats->sum('entregas');

        // Períodos guardados
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

        return view('driver-earnings.index', compact(
            'earnings',
            'drivers',
            'stats',
            'totalGanancias',
            'totalPropinas',
            'totalEntregas'
        ));
    }

    public function show($id)
    {
        $earning = DriverEarning::with(['driver', 'details.orden', 'details.venta'])->findOrFail($id);
        $details = $earning->details;

        return view('driver-earnings.show', compact('earning', 'details'));
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

        $driver = DeliveryDriver::where('id', $data['driver_id'])
            ->where('tenant_id', Auth::user()->business_instance_id)
            ->first();

        if (! $driver) {
            return back()->with('error', 'Driver no encontrado en esta instancia.');
        }

        $driverId = $data['driver_id'];
        $inicio = $data['periodo_inicio'];
        $fin = $data['periodo_fin'];

        $ventas = Venta::where('driver_id', $driverId)
            ->where('tipo_orden', 'delivery')
            ->where('estado', 'completada')
            ->whereBetween('created_at', [$inicio, $fin.' 23:59:59'])
            ->get();

        $totalGanancias = 0;
        $totalEntregas = $ventas->count();
        $detalles = [];

        foreach ($ventas as $venta) {
            $ganancia = floatval($venta->delivery_fee ?? 0);
            $propina = floatval($venta->propina ?? 0);

            $totalGanancias += $ganancia + $propina;

            $detalles[] = [
                'venta_id' => $venta->id,
                'orden_id' => $venta->orden?->id,
                'monto_ganancia' => round($ganancia, 2),
                'propina' => round($propina, 2),
                'fecha' => $venta->created_at?->format('Y-m-d H:i:s'),
            ];
        }

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
            $query->whereHas('earning', fn ($q) => $q->where('driver_id', $driverId));
        }

        $query->whereHas('earning', fn ($q) => $q
            ->where('periodo_inicio', '>=', $data['periodo_inicio'])
            ->where('periodo_fin', '<=', $data['periodo_fin'])
        );

        $details = $query->get();

        $response = new StreamedResponse(function () use ($details) {
            $handle = fopen('php://output', 'w');
            stream_set_encoding($handle, 'UTF-8');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

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

            foreach ($details as $detail) {
                $driverName = $detail->earning?->driver ? $detail->earning->driver->nombreCompleto : 'N/A';
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
        $response->headers->set('Content-Disposition', 'attachment; filename="ganancias_drivers_'.date('Y-m-d').'.csv"');

        return $response;
    }
}
