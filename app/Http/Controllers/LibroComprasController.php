<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Services\RetentionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LibroComprasController extends Controller
{
    public function __construct(protected RetentionService $retentionService) {}

    public function index(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $query = Compra::with(['proveedor', 'almacen', 'user'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->when($request->filled('proveedor'), function ($q) use ($request) {
                $term = $request->proveedor;
                $q->whereHas('proveedor', fn($qq) => $qq->where('nombre', 'like', "%{$term}%")
                    ->orWhere('rnc', 'like', "%{$term}%"));
            })
            ->when($request->filled('tipo_compra'), function ($q) use ($request) {
                $q->where('tipo_compra_id', $request->tipo_compra);
            });

        $compras = $query->orderBy('fecha')->paginate(50)->appends($request->all());

        // Totales por proveedor
        $totalesProveedor = Compra::selectRaw('proveedor_id, COUNT(*) as cantidad, SUM(subtotal) as subtotal, SUM(itbis_total) as itbis, SUM(retencion_itbis) as itbis_retenido, SUM(retencion_isr) as isr_retenido, SUM(total) as total_compras')
            ->whereBetween('fecha', [$desde, $hasta])
            ->groupBy('proveedor_id')
            ->with('proveedor:id,nombre,rnc')
            ->get();

        // Resumen general
        $resumenGeneral = Compra::selectRaw('COUNT(*) as total, SUM(subtotal) as gran_subtotal, SUM(itbis_total) as gran_itbis, SUM(retencion_itbis) as gran_itbis_retenido, SUM(retencion_isr) as gran_isr_retenido, SUM(total) as gran_total')
            ->whereBetween('fecha', [$desde, $hasta])
            ->first();

        // Retenciones por tipo
        $retencionesResumen = [
            'itbis' => Compra::whereBetween('fecha', [$desde, $hasta])->sum('retencion_itbis'),
            'isr' => Compra::whereBetween('fecha', [$desde, $hasta])->sum('retencion_isr'),
        ];

        return view('libros.compras.index', compact(
            'compras', 'totalesProveedor', 'resumenGeneral', 'retencionesResumen',
            'mes', 'anio', 'desde', 'hasta'
        ));
    }

    public function exportCsv(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $compras = Compra::with(['proveedor', 'user'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->get();

        $filename = "libro_compras_{$anio}_{$mes}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($compras) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                '#', 'Fecha', 'Proveedor', 'RNC', 'Tipo Persona',
                'Subtotal', 'ITBIS', 'Retención ITBIS', 'Retención ISR',
                'Total', 'Total Neto', 'Observaciones'
            ]);

            foreach ($compras as $i => $c) {
                fputcsv($file, [
                    $i + 1,
                    $c->fecha->format('Y-m-d'),
                    $c->proveedor->nombre ?? '',
                    $c->proveedor->rnc ?? '',
                    $c->proveedor->tipo_persona ?? 'juridica',
                    number_format($c->subtotal, 2, '.', ''),
                    number_format($c->itbis_total, 2, '.', ''),
                    number_format($c->retencion_itbis, 2, '.', ''),
                    number_format($c->retencion_isr, 2, '.', ''),
                    number_format($c->total, 2, '.', ''),
                    number_format($c->total_neto ?? $c->total, 2, '.', ''),
                    $c->observaciones ?? '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $compras = Compra::with(['proveedor', 'user', 'almacen'])
            ->whereBetween('fecha', [$desde, $hasta])
            ->orderBy('fecha')
            ->get();

        $resumenGeneral = Compra::selectRaw('COUNT(*) as total, SUM(subtotal) as gran_subtotal, SUM(itbis_total) as gran_itbis, SUM(retencion_itbis) as gran_itbis_retenido, SUM(retencion_isr) as gran_isr_retenido, SUM(total) as gran_total')
            ->whereBetween('fecha', [$desde, $hasta])
            ->first();

        $mesNombre = \Carbon\Carbon::create($anio, $mes, 1)->format('F');

        $pdf = Pdf::loadView('libros.compras.pdf', compact(
            'compras', 'resumenGeneral', 'mes', 'anio', 'mesNombre'
        ));

        return $pdf->download("libro_compras_{$anio}_{$mes}.pdf");
    }
}
