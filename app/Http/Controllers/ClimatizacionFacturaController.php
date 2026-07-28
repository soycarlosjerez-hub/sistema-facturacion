<?php

namespace App\Http\Controllers;

use App\Models\ClimatizacionFactura;
use App\Models\Cliente;
use App\Services\ClimatizacionFacturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClimatizacionFacturaController extends Controller
{
    public function __construct(private ClimatizacionFacturaService $facturaService) {}

    public function index(Request $request)
    {
        $query = ClimatizacionFactura::query()
            ->with(['cliente', 'creadoPor'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('origen')) {
            $query->where('origen', $request->origen);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . $request->cliente . '%'));
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $facturas = $query->paginate(20)->withQueryString();
        
        $combinedStats = ClimatizacionFactura::selectRaw('
            SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN total ELSE 0 END) as total_mes,
            COUNT(CASE WHEN estado = ? THEN 1 END) as borradores,
            COUNT(CASE WHEN estado = ? THEN 1 END) as generadas
        ', [now()->month, now()->year, 'borrador', 'generada'])->first();

        $stats = [
            'total_mes' => $combinedStats->total_mes ?? 0,
            'pendientes' => $combinedStats->borradores ?? 0,
            'generadas' => $combinedStats->generadas ?? 0,
        ];

        return view('climatizacion.facturas.index', compact('facturas', 'stats'));
    }

    public function show(ClimatizacionFactura $climatizacionFactura)
    {
        $climatizacionFactura->load(['cliente', 'creadoPor']);
        return view('climatizacion.facturas.show', compact('climatizacionFactura'));
    }

    public function crearDesdeMantenimiento(\App\Models\Mantenimiento $mantenimiento)
    {
        try {
            $factura = $this->facturaService->generarDesdeMantenimiento($mantenimiento);
            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura borrador creada exitosamente. Total: RD$ ' . number_format($factura->total, 2));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generarDesdeContrato(\App\Models\ContratoMantenimiento $contrato)
    {
        try {
            $factura = $this->facturaService->generarDesdeContrato($contrato);
            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de cuota generada. Total: RD$ ' . number_format($factura->total, 2));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generarDesdeEmergencia(\App\Models\OrdenEmergencia $orden)
    {
        try {
            $factura = $this->facturaService->generarDesdeEmergencia($orden);
            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de emergencia generada. Total: RD$ ' . number_format($factura->total, 2));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generarDesdeInstalacion(\App\Models\Instalacion $instalacion)
    {
        try {
            $factura = $this->facturaService->generarDesdeInstalacion($instalacion);
            return redirect()->route('climatizacion.facturas.show', $factura)
                ->with('success', 'Factura de instalación generada. Total: RD$ ' . number_format($factura->total, 2));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function anular(ClimatizacionFactura $climatizacionFactura)
    {
        try {
            $this->facturaService->anular($climatizacionFactura);
            return redirect()->route('climatizacion.facturas.index')
                ->with('success', 'Factura anulada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function generar(ClimatizacionFactura $climatizacionFactura)
    {
        try {
            $this->facturaService->generar($climatizacionFactura);
            return response()->json([
                'success' => true,
                'message' => 'Factura generada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
