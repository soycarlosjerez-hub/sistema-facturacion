<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Sucursal;
use App\Services\ReporteService;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        protected ReporteService $reporteService
    ) {}

    public function index()
    {
        return view('reportes.index', $this->reporteService->resumen());
    }

    public function ventas(Request $request)
    {
        $data = $this->reporteService->ventas(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.ventas', $data);
    }

    public function ventasCsv(Request $request)
    {
        $data = $this->reporteService->ventas(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );

        return $this->reporteService->exportCsv(
            ['filename' => "ventas_{$data['desde']}_{$data['hasta']}.csv"],
            ['#', 'Cliente', 'RNC', 'Vendedor', 'NCF', 'Fecha', 'Subtotal', 'ITBIS', 'Total', 'Efectivo', 'Método'],
            function ($output) use ($data) {
                foreach ($data['ventas'] as $i => $v) {
                    fputcsv($output, [
                        $i + 1, $v->cliente?->nombre ?? 'Consumidor Final', $v->cliente?->rnc_cedula ?? '',
                        $v->user?->name ?? '', $v->ncf ?? $v->encf ?? 'S/N',
                        $v->created_at->format('d/m/Y H:i'),
                        number_format($v->subtotal ?? 0, 2, '.', ''),
                        number_format($v->impuestos ?? 0, 2, '.', ''),
                        number_format($v->total, 2, '.', ''),
                        number_format($v->total ?? 0, 2, '.', ''),
                        $v->metodo_pago ?? 'Efectivo',
                    ]);
                }
            }
        );
    }

    public function ventasPdf(Request $request)
    {
        $data = $this->reporteService->ventas(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return $this->exportPdf('reportes.ventas-pdf', $data, "ventas_{$data['desde']}_{$data['hasta']}.pdf");
    }

    public function gastos(Request $request)
    {
        $data = $this->reporteService->gastos(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d')),
            $request->input('categoria')
        );
        return view('reportes.gastos', $data);
    }

    public function gastosCsv(Request $request)
    {
        $data = $this->reporteService->gastos(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d')),
            $request->input('categoria')
        );

        return $this->reporteService->exportCsv(
            ['filename' => "gastos_{$data['desde']}_{$data['hasta']}.csv"],
            ['#', 'Descripción', 'Categoría', 'Método de Pago', 'Comprobante', 'Usuario', 'Fecha', 'Monto'],
            function ($output) use ($data) {
                foreach ($data['gastos'] as $i => $g) {
                    fputcsv($output, [
                        $i + 1,
                        $g->descripcion,
                        $g->categoria ?? '',
                        $g->metodo_pago ?? '',
                        $g->comprobante ?? '',
                        $g->user?->name ?? '',
                        $g->fecha_gasto?->format('d/m/Y') ?? '',
                        number_format($g->monto, 2, '.', ''),
                    ]);
                }
            }
        );
    }

    public function gastosPdf(Request $request)
    {
        $data = $this->reporteService->gastos(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d')),
            $request->input('categoria')
        );
        return $this->exportPdf('reportes.gastos-pdf', $data, "gastos_{$data['desde']}_{$data['hasta']}.pdf");
    }

    public function compras(Request $request)
    {
        $data = $this->reporteService->compras(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.compras', $data);
    }

    public function comprasCsv(Request $request)
    {
        $data = $this->reporteService->compras(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );

        return $this->reporteService->exportCsv(
            ['filename' => "compras_{$data['desde']}_{$data['hasta']}.csv"],
            ['#', 'Proveedor', 'RNC', 'Usuario', 'Folio', 'Fecha', 'Subtotal', 'ITBIS', 'Ret ISR', 'Ret ITBIS', 'Total'],
            function ($output) use ($data) {
                foreach ($data['compras'] as $i => $c) {
                    fputcsv($output, [
                        $i + 1, $c->proveedor?->nombre ?? 'N/A', $c->proveedor?->rnc ?? '',
                        $c->user?->name ?? '', $c->folio ?? '', $c->fecha?->format('d/m/Y') ?? '',
                        number_format($c->subtotal ?? 0, 2, '.', ''),
                        number_format($c->itbis_total ?? 0, 2, '.', ''),
                        number_format($c->retencion_isr ?? 0, 2, '.', ''),
                        number_format($c->retencion_itbis ?? 0, 2, '.', ''),
                        number_format($c->total, 2, '.', ''),
                    ]);
                }
            }
        );
    }

    public function comprasPdf(Request $request)
    {
        $data = $this->reporteService->compras(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return $this->exportPdf('reportes.compras-pdf', $data, "compras_{$data['desde']}_{$data['hasta']}.pdf");
    }

    public function stock(Request $request)
    {
        $data = $this->reporteService->stock(
            $request->input('filtro', 'todos'),
            $request->input('buscar')
        );
        return view('reportes.stock', $data);
    }

    public function stockCsv(Request $request)
    {
        $data = $this->reporteService->stock(
            $request->input('filtro', 'todos'),
            $request->input('buscar')
        );

        return $this->reporteService->exportCsv(
            ['filename' => "inventario_" . now()->format('Ymd') . ".csv"],
            ['Código', 'Nombre', 'Categoría', 'Stock Actual', 'Stock Mínimo', 'Costo', 'Precio Venta', 'Valor Inventario', 'Estado'],
            function ($output) use ($data) {
                foreach ($data['productos'] as $p) {
                    $estado = $p->stock <= 0 ? 'Sin Stock' : ($p->stock <= ($p->stock_minimo ?? 0) ? 'Stock Bajo' : 'Disponible');
                    fputcsv($output, [
                        $p->codigo_barras ?? $p->referencia ?? '', $p->nombre,
                        $p->categoria?->nombre ?? '', $p->stock, $p->stock_minimo ?? 0,
                        number_format($p->precio_compra ?? 0, 2, '.', ''),
                        number_format($p->precio ?? 0, 2, '.', ''),
                        number_format(($p->stock * ($p->precio_compra ?? 0)), 2, '.', ''),
                        $estado,
                    ]);
                }
            }
        );
    }

    public function stockPdf(Request $request)
    {
        $data = $this->reporteService->stock(
            $request->input('filtro', 'todos'),
            $request->input('buscar')
        );
        return $this->exportPdf('reportes.stock-pdf', $data, "inventario_" . now()->format('Ymd') . ".pdf");
    }

    public function caja(Request $request)
    {
        $data = $this->reporteService->caja(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d')),
            $request->input('caja_id')
        );
        return view('reportes.caja', $data);
    }

    public function cajaCsv(Request $request)
    {
        $data = $this->reporteService->caja(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d')),
            $request->input('caja_id')
        );

        return $this->reporteService->exportCsv(
            ['filename' => "caja_{$data['desde']}_{$data['hasta']}.csv"],
            ['Caja', 'Cajero', 'Apertura', 'Cierre', 'Inicial', 'Efectivo', 'Tarjeta', 'Transferencia', 'Declarado', 'Descuadre', 'Estado'],
            function ($output) use ($data) {
                foreach ($data['sesiones'] as $s) {
                    fputcsv($output, [
                        $s->caja?->nombre ?? '', $s->user?->name ?? '',
                        $s->fecha_apertura?->format('d/m/Y H:i') ?? '',
                        $s->fecha_cierre?->format('d/m/Y H:i') ?? '',
                        number_format($s->monto_inicial ?? 0, 2, '.', ''),
                        number_format($s->ventas_efectivo ?? 0, 2, '.', ''),
                        number_format($s->ventas_tarjeta ?? 0, 2, '.', ''),
                        number_format($s->ventas_transferencia ?? 0, 2, '.', ''),
                        number_format($s->monto_declarado ?? 0, 2, '.', ''),
                        number_format($s->descuadre ?? 0, 2, '.', ''),
                        $s->estado ?? '',
                    ]);
                }
            }
        );
    }

    public function utilidades(Request $request)
    {
        $data = $this->reporteService->utilidades(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.utilidades', $data);
    }

    public function utilidadesCsv(Request $request)
    {
        $data = $this->reporteService->utilidades(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );

        return $this->reporteService->exportCsv(
            ['filename' => "utilidades_{$data['desde']}_{$data['hasta']}.csv"],
            ['Venta', 'Fecha', 'Cliente', 'Producto', 'Cantidad', 'Precio', 'Costo', 'Subtotal', 'Ganancia'],
            function ($output) use ($data) {
                foreach ($data['detalles'] as $d) {
                    fputcsv($output, [
                        $d['venta_id'], $d['fecha'], $d['cliente'], $d['producto'],
                        $d['cantidad'],
                        number_format($d['precio'], 2, '.', ''),
                        number_format($d['costo'], 2, '.', ''),
                        number_format($d['subtotal'], 2, '.', ''),
                        number_format($d['ganancia'], 2, '.', ''),
                    ]);
                }
            }
        );
    }

    public function retenciones(Request $request)
    {
        $data = $this->reporteService->retenciones(
            (int) $request->input('mes', now()->month),
            (int) $request->input('anio', now()->year),
            $request->input('tipo', 'compras')
        );
        return view('reportes.retenciones', $data);
    }

    public function retencionesCsv(Request $request)
    {
        $data = $this->reporteService->retenciones(
            (int) $request->input('mes', now()->month),
            (int) $request->input('anio', now()->year),
            $request->input('tipo', 'compras')
        );

        return $this->reporteService->exportCsv(
            ['filename' => "retenciones_{$data['tipo']}_{$data['anio']}_{$data['mes']}.csv"],
            [],
            function ($output) use ($data) {
                if (in_array($data['tipo'], ['compras', 'ambos'])) {
                    fputcsv($output, ['Tipo', 'Proveedor', 'RNC', 'Documento', 'Fecha', 'Total', 'Ret ISR', 'Ret ITBIS', 'Total Retenido']);
                    foreach ($data['compras'] as $c) {
                        fputcsv($output, [
                            'Compra', $c->proveedor?->nombre ?? 'N/A', $c->proveedor?->rnc ?? '',
                            $c->folio ?? '#' . $c->id, $c->fecha?->format('d/m/Y') ?? '',
                            number_format($c->total, 2, '.', ''),
                            number_format($c->retencion_isr ?? 0, 2, '.', ''),
                            number_format($c->retencion_itbis ?? 0, 2, '.', ''),
                            number_format(($c->retencion_isr ?? 0) + ($c->retencion_itbis ?? 0), 2, '.', ''),
                        ]);
                    }
                }
                if (in_array($data['tipo'], ['ventas', 'ambos'])) {
                    if ($data['tipo'] === 'ambos') fputcsv($output, []);
                    fputcsv($output, ['Tipo', 'Cliente', 'RNC', 'Documento', 'Fecha', 'Total', 'Ret ISR', 'Ret ITBIS', 'Total Retenido']);
                    foreach ($data['ventas'] as $v) {
                        fputcsv($output, [
                            'Venta', $v->cliente?->nombre ?? 'N/A', $v->cliente?->rnc_cedula ?? '',
                            '#' . str_pad($v->id, 5, '0', STR_PAD_LEFT), $v->created_at->format('d/m/Y'),
                            number_format($v->total, 2, '.', ''),
                            number_format($v->retencion_isr ?? 0, 2, '.', ''),
                            number_format($v->retencion_itbis ?? 0, 2, '.', ''),
                            number_format(($v->retencion_isr ?? 0) + ($v->retencion_itbis ?? 0), 2, '.', ''),
                        ]);
                    }
                }
            }
        );
    }

    public function restaurante(Request $request)
    {
        $data = $this->reporteService->restaurante(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.restaurante', $data);
    }

    public function propinas(Request $request)
    {
        $data = $this->reporteService->propinas(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.propinas', $data);
    }

    public function comisionesDelivery(Request $request)
    {
        $data = $this->reporteService->comisionesDelivery(
            $request->input('desde', today()->startOfMonth()->format('Y-m-d')),
            $request->input('hasta', today()->format('Y-m-d'))
        );
        return view('reportes.delivery-comisiones', $data);
    }

    private function exportPdf(string $view, array $data, string $filename)
    {
        $html = view($view, $data)->render();
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->stream($filename);
    }
}
