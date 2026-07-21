<?php

namespace App\Http\Controllers;

use App\Services\RetentionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class Formulario1414Controller extends Controller
{
    public function __construct(protected RetentionService $retentionService) {}

    public function index(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);

        $sucursalId = session('sucursal_id');
        $resumen = $this->retentionService->generarResumenRetenciones($mes, $anio, $sucursalId);

        // Obtener proveedores con retenciones
        $proveedores = \App\Models\Compra::select('proveedor_id')
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where(function($q) {
                $q->where('retencion_itbis', '>', 0)
                  ->orWhere('retencion_isr', '>', 0);
            })
            ->distinct()
            ->with('proveedor')
            ->get();

        return view('formularios.14-14.index', compact('resumen', 'proveedores', 'mes', 'anio'));
    }

    public function exportPdf(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);

        $sucursalId = session('sucursal_id');
        $resumen = $this->retentionService->generarResumenRetenciones($mes, $anio, $sucursalId);
        $empresa = \App\Models\SystemSetting::allCached();

        $mesNombre = \Carbon\Carbon::create($anio, $mes, 1)->format('F');

        $pdf = Pdf::loadView('formularios.14-14.pdf', compact(
            'resumen', 'empresa', 'mes', 'anio', 'mesNombre'
        ));

        return $pdf->download("formulario_14-14_{$anio}_{$mes}.pdf");
    }

    public function exportCsv(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);

        $sucursalId = session('sucursal_id');

        $compras = \App\Models\Compra::with('proveedor')
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->when($sucursalId, fn($q) => $q->where('sucursal_id', $sucursalId))
            ->where(function($q) {
                $q->where('retencion_itbis', '>', 0)
                  ->orWhere('retencion_isr', '>', 0);
            })
            ->orderBy('fecha')
            ->get();

        $filename = "form_14-14_{$anio}_{$mes}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($compras) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Fecha', 'Proveedor', 'RNC', 'Tipo Persona',
                'Base Imponible', 'ITBIS Retenido', 'ISR Retenido',
                'Total Retenido', 'Comprobante Retención'
            ]);

            foreach ($compras as $c) {
                $totalRetenido = (float)$c->retencion_itbis + (float)$c->retencion_isr;
                
                // Generar código de comprobante
                $secuencial = $c->id;
                $codigoComp = \App\Support\RetencionCalculator::generarCodigoComprobante($c->fecha, $secuencial);

                fputcsv($file, [
                    $c->fecha->format('Y-m-d'),
                    $c->proveedor->nombre ?? '',
                    $c->proveedor->rnc ?? '',
                    $c->proveedor->tipo_persona ?? 'juridica',
                    number_format($c->subtotal, 2, '.', ''),
                    number_format($c->retencion_itbis, 2, '.', ''),
                    number_format($c->retencion_isr, 2, '.', ''),
                    number_format($totalRetenido, 2, '.', ''),
                    $codigoComp,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
