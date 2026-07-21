<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Services\RetentionService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LibroVentasController extends Controller
{
    public function __construct(protected RetentionService $retentionService) {}

    public function index(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $query = Venta::with(['cliente', 'usuario', 'caja', 'sucursal'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->when($request->filled('cliente'), function ($q) use ($request) {
                $term = $request->cliente;
                $q->whereHas('cliente', fn($qq) => $qq->where('nombre', 'like', "%{$term}%")
                    ->orWhere('rnc_cedula', 'like', "%{$term}%"));
            })
            ->when($request->filled('tipo_ncf'), function ($q) use ($request) {
                $q->where('ncf_tipo', $request->tipo_ncf);
            })
            ->when($request->filled('estado'), function ($q) use ($request) {
                $q->where('estado', $request->estado);
            });

        $ventas = $query->orderBy('created_at')->paginate(50)->appends($request->all());

        // Totales agrupados por tipo de NCF
        $totales = Venta::selectRaw('ncf_tipo, COUNT(*) as cantidad, SUM(total) as total_ventas, SUM(subtotal) as subtotal, SUM(impuestos) as itbis_total')
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->groupBy('ncf_tipo')
            ->get();

        // Resumen general
        $resumenGeneral = Venta::selectRaw('COUNT(*) as total, SUM(total) as gran_total, SUM(subtotal) as gran_subtotal, SUM(impuestos) as gran_itbis')
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->first();

        // Ventas por método de pago
        $pagosMetodo = Venta::join('pagos', 'ventas.id', '=', 'pagos.venta_id')
            ->whereBetween('ventas.created_at', [$desde, $hasta])
            ->whereNotNull('ventas.deleted_at')
            ->selectRaw('pagos.metodo_pago, COUNT(DISTINCT pagos.venta_id) as cantidad, SUM(pagos.monto) as total')
            ->groupBy('pagos.metodo_pago')
            ->get();

        return view('libros.ventas.index', compact(
            'ventas', 'totales', 'resumenGeneral', 'pagosMetodo',
            'mes', 'anio', 'desde', 'hasta'
        ));
    }

    public function exportCsv(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $desde = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth()->format('Y-m-d');
        $hasta = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth()->format('Y-m-d');

        $ventas = Venta::with(['cliente', 'usuario'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        $filename = "libro_ventas_{$anio}_{$mes}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($ventas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                '#', 'Fecha', 'NCF', 'Tipo NCF', 'Cliente', 'RNC/Cedula',
                'Tipo Cliente', 'Subtotal', 'ITBIS', 'Descuento', 'Total',
                'Encf', 'Estado', 'Vendedor', 'Caja'
            ]);

            foreach ($ventas as $i => $v) {
                fputcsv($file, [
                    $i + 1,
                    $v->created_at->format('Y-m-d'),
                    $v->ncf ?? 'S/N',
                    strtoupper($v->ncf_tipo ?? 'CONSUMER'),
                    $v->cliente->nombre ?? 'Consumidor Final',
                    $v->cliente->rnc_cedula ?? '00000000000',
                    $v->cliente->tipo_cliente ?? 'consumo',
                    number_format($v->subtotal, 2, '.', ''),
                    number_format($v->impuestos, 2, '.', ''),
                    number_format($v->descuento, 2, '.', ''),
                    number_format($v->total, 2, '.', ''),
                    $v->encf ?? '',
                    strtoupper($v->estado),
                    $v->usuario->name ?? '',
                    $v->caja->nombre ?? '',
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

        $ventas = Venta::with(['cliente', 'usuario', 'caja', 'sucursal'])
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->orderBy('created_at')
            ->get();

        $totales = Venta::selectRaw('ncf_tipo, COUNT(*) as cantidad, SUM(total) as total_ventas, SUM(subtotal) as subtotal, SUM(impuestos) as itbis_total')
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->groupBy('ncf_tipo')
            ->get();

        $resumenGeneral = Venta::selectRaw('COUNT(*) as total, SUM(total) as gran_total, SUM(subtotal) as gran_subtotal, SUM(impuestos) as gran_itbis')
            ->whereBetween('created_at', [$desde, $hasta])
            ->whereNotNull('deleted_at')
            ->first();

        $mesNombre = \Carbon\Carbon::create($anio, $mes, 1)->format('F');

        $pdf = Pdf::loadView('libros.ventas.pdf', compact(
            'ventas', 'totales', 'resumenGeneral', 'mes', 'anio', 'mesNombre'
        ));

        return $pdf->download("libro_ventas_{$anio}_{$mes}.pdf");
    }
}
