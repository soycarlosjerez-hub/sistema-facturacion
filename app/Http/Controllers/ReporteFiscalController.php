<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\EcfDocumento;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReporteFiscalController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $tipo = $request->input('tipo', '607');

        $data = $tipo === '607'
            ? $this->generar607($mes, $anio)
            : $this->generar606($mes, $anio);

        return view('reportes.fiscales', array_merge(
            $data,
            compact('mes', 'anio', 'tipo')
        ));
    }

    public function exportCsv(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $tipo = $request->input('tipo', '607');

        $data = $tipo === '607'
            ? $this->generar607($mes, $anio)
            : $this->generar606($mes, $anio);

        $filename = "{$tipo}_{$anio}_{$mes}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($data, $tipo) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($tipo === '607') {
                fputcsv($output, ['RNC Cliente', 'Nombre Cliente', 'NCF/e-CF', 'Tipo Comprobante', 'Fecha', 'Monto Facturado', 'ITBIS', 'Total', 'Tipo ID', 'NCF Modificado', 'Retención ISR', 'Retención ITBIS']);
                foreach ($data['registros'] as $r) {
                    fputcsv($output, [
                        $r['rnc'], $r['cliente'], $r['ncf'], $r['tipo_ncf'], $r['fecha'],
                        number_format($r['monto_facturado'], 2, '.', ''),
                        number_format($r['itbis'], 2, '.', ''),
                        number_format($r['total'], 2, '.', ''),
                        $r['tipo_id'], $r['ncf_modificado'] ?? '',
                        number_format($r['retencion_isr'] ?? 0, 2, '.', ''),
                        number_format($r['retencion_itbis'] ?? 0, 2, '.', ''),
                    ]);
                }
            } else {
                fputcsv($output, ['RNC Proveedor', 'Nombre Proveedor', 'NCF', 'Tipo Comprobante', 'Fecha', 'Monto Facturado', 'ITBIS', 'Total', 'Tipo ID', 'NCF Modificado', 'Retención ISR', 'Retención ITBIS']);
                foreach ($data['registros'] as $r) {
                    fputcsv($output, [
                        $r['rnc'], $r['proveedor'], $r['ncf'] ?? 'S/N', $r['tipo_ncf'], $r['fecha'],
                        number_format($r['monto_facturado'], 2, '.', ''),
                        number_format($r['itbis'], 2, '.', ''),
                        number_format($r['total'], 2, '.', ''),
                        $r['tipo_id'], $r['ncf_modificado'] ?? '',
                        number_format($r['retencion_isr'] ?? 0, 2, '.', ''),
                        number_format($r['retencion_itbis'] ?? 0, 2, '.', ''),
                    ]);
                }
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTxt(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $tipo = $request->input('tipo', '607');

        $data = $tipo === '607'
            ? $this->generar607($mes, $anio)
            : $this->generar606($mes, $anio);

        $filename = "{$tipo}_{$anio}_{str_pad($mes,2,'0','STR_PAD_LEFT)}.txt";
        $headers = [
            'Content-Type' => 'text/plain; charset=iso-8859-1',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $config = app(\App\Models\SystemSetting::class)->allCached();
        $rnc = preg_replace('/[^0-9]/', '', $config['rnc_empresa'] ?? '000000000');
        $rnc = str_pad(substr($rnc, 0, 9), 9, '0');

        $lines = [];
        // Header
        $lines[] = $tipo === '607'
            ? "{$rnc}|{$anio}|{$mes}|0.00|0.00|0.00"
            : "{$rnc}|{$anio}|{$mes}|0.00|0.00|0.00";

        foreach ($data['registros'] as $r) {
            if ($tipo === '607') {
                $lines[] = implode('|', [
                    $r['rnc'],
                    $this->toIso($r['cliente']),
                    $r['ncf'],
                    $r['tipo_comprobante_codigo'] ?? $r['tipo_ncf'],
                    $r['fecha'],
                    number_format($r['monto_facturado'], 2, '.', ''),
                    number_format($r['itbis'], 2, '.', ''),
                    number_format($r['total'], 2, '.', ''),
                    $r['tipo_id'],
                    $r['ncf_modificado'] ?? '',
                    number_format($r['retencion_isr'] ?? 0, 2, '.', ''),
                    number_format($r['retencion_itbis'] ?? 0, 2, '.', ''),
                ]);
            } else {
                $lines[] = implode('|', [
                    $r['rnc'],
                    $this->toIso($r['proveedor']),
                    $r['ncf'] ?? 'S/N',
                    $r['tipo_comprobante_codigo'] ?? '02',
                    $r['fecha'],
                    number_format($r['monto_facturado'], 2, '.', ''),
                    number_format($r['itbis'], 2, '.', ''),
                    number_format($r['total'], 2, '.', ''),
                    $r['tipo_id'],
                    $r['ncf_modificado'] ?? '',
                    number_format($r['retencion_isr'] ?? 0, 2, '.', ''),
                    number_format($r['retencion_itbis'] ?? 0, 2, '.', ''),
                ]);
            }
        }

        $content = implode("\r\n", $lines) . "\r\n";
        $content = mb_convert_encoding($content, 'ISO-8859-1', 'UTF-8');

        return response($content, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $mes = $request->input('mes', now()->month);
        $anio = $request->input('anio', now()->year);
        $tipo = $request->input('tipo', '607');

        $data = $tipo === '607'
            ? $this->generar607($mes, $anio)
            : $this->generar606($mes, $anio);

        $html = view('reportes.fiscales-pdf', array_merge(
            $data,
            compact('mes', 'anio', 'tipo')
        ))->render();

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->stream("{$tipo}_{$anio}_{$mes}.pdf");
    }

    private function generar607(int $mes, int $anio): array
    {
        $periodo = Carbon::create($anio, $mes, 1);

        // Ventas con NCF
        $ventas = Venta::whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->where('total', '>', 0)
            ->with('cliente:id,nombre,rnc_cedula,tipo_documento')
            ->orderBy('created_at')
            ->get();

        // e-CF emitidos
        $ecfs = EcfDocumento::whereMonth('fecha_emision', $mes)
            ->whereYear('fecha_emision', $anio)
            ->whereIn('tipo_ecf', ['E31', 'E32', 'E33', 'E34', 'E41', 'E44', 'E45'])
            ->with('venta.cliente')
            ->orderBy('fecha_emision')
            ->get();

        $registros = collect();

        foreach ($ventas as $v) {
            $rnc = $v->cliente?->rnc_cedula ?? '000000000';
            $rnc = preg_replace('/[^0-9]/', '', $rnc);
            if (strlen($rnc) < 9) $rnc = str_pad($rnc, 9, '0');

            $tipoNcf = $this->tipoComprobanteLabel($v->ncf_tipo ?? ($v->tipo_comprobante ?? 'ncf'));
            $tipoId = $this->tipoIdentificacion($rnc);

            $registros->push([
                'rnc' => $rnc,
                'cliente' => Str::limit($v->cliente?->nombre ?? 'Consumidor Final', 40),
                'ncf' => $v->encf ?? $v->ncf ?? 'S/N',
                'tipo_ncf' => $tipoNcf,
                'tipo_comprobante_codigo' => $v->tipo_comprobante === 'ecf' ? $v->ecfDocumento?->tipo_ecf : ($v->ncf_tipo ?? '02'),
                'fecha' => $v->created_at->format('d/m/Y'),
                'monto_facturado' => $v->subtotal ?? 0,
                'itbis' => $v->impuestos ?? 0,
                'impuestos' => $v->impuestos ?? 0,
                'total' => $v->total,
                'tipo_id' => $tipoId,
                'ncf_modificado' => '',
                'retencion_isr' => $v->retencion_isr ?? 0,
                'retencion_itbis' => $v->retencion_itbis ?? 0,
            ]);
        }

        foreach ($ecfs as $ecf) {
            $rnc = $ecf->venta?->cliente?->rnc_cedula ?? '000000000';
            $rnc = preg_replace('/[^0-9]/', '', $rnc);
            if (strlen($rnc) < 9) $rnc = str_pad($rnc, 9, '0');

            $cliente = $ecf->venta?->cliente;
            $tipoId = $this->tipoIdentificacion($rnc);
            $tipoDoc = $cliente?->tipo_documento ? ucfirst($cliente->tipo_documento) : $tipoId;

            $ncfModificado = '';
            if (in_array($ecf->tipo_ecf, ['E34', 'E33']) && $ecf->documento_original_id) {
                $orig = EcfDocumento::find($ecf->documento_original_id);
                $ncfModificado = $orig?->encf ?? '';
            }

            // Evitar duplicados con ventas
            $exists = $registros->first(fn($r) => $r['ncf'] === $ecf->encf);
            if (!$exists) {
                $registros->push([
                    'rnc' => $rnc,
                    'cliente' => Str::limit($cliente?->nombre ?? 'Consumidor Final', 40),
                    'ncf' => $ecf->encf,
                    'tipo_ncf' => $ecf->tipo_nombre,
                    'tipo_comprobante_codigo' => $ecf->tipo_ecf,
                    'fecha' => $ecf->fecha_emision->format('d/m/Y'),
                    'monto_facturado' => $ecf->monto_gravado_total + $ecf->monto_exento_total,
                    'itbis' => $ecf->itbis_total,
                    'impuestos' => $ecf->itbis_total,
                    'total' => $ecf->monto_total,
                    'tipo_id' => $tipoDoc === 'RNC' ? '1' : ($tipoDoc === 'CEDULA' ? '2' : ($tipoDoc === '1' ? '1' : ($tipoDoc === '2' ? '2' : '3'))),
                    'ncf_modificado' => $ncfModificado,
                    'retencion_isr' => 0,
                    'retencion_itbis' => 0,
                ]);
            }
        }

        $registros = $registros->sortBy('fecha')->values();

        return [
            'registros' => $registros,
            'total_monto' => $registros->sum('monto_facturado'),
            'total_itbis' => $registros->sum('itbis'),
            'total_impuestos' => $registros->sum('impuestos'),
            'total_general' => $registros->sum('total'),
            'cantidad' => $registros->count(),
            'periodo' => $periodo,
            'titulo' => 'Formato 607 - Ventas e Ingresos',
        ];
    }

    private function generar606(int $mes, int $anio): array
    {
        $periodo = Carbon::create($anio, $mes, 1);

        $compras = Compra::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->where('total', '>', 0)
            ->with('proveedor:id,nombre,rnc,rnc_cedula,tipo_persona')
            ->orderBy('fecha')
            ->get();

        $registros = $compras->map(function ($c) use ($periodo) {
            $rnc = $c->proveedor?->rnc ?? $c->proveedor?->rnc_cedula ?? '000000000';
            $rnc = preg_replace('/[^0-9]/', '', $rnc);
            if (strlen($rnc) < 9) $rnc = str_pad($rnc, 9, '0');

            $tipoNcf = 'Compras';
            $tipoId = $this->tipoIdentificacion($rnc);

            return [
                'rnc' => $rnc,
                'proveedor' => Str::limit($c->proveedor?->nombre ?? 'Proveedor', 40),
                'ncf' => 'S/N',
                'tipo_ncf' => $tipoNcf,
                'tipo_comprobante_codigo' => '02',
                'fecha' => Carbon::parse($c->fecha)->format('d/m/Y'),
                'monto_facturado' => $c->subtotal ?? 0,
                'itbis' => $c->itbis_total ?? 0,
                'impuestos' => $c->itbis_total ?? 0,
                'total' => $c->total,
                'tipo_id' => $tipoId,
                'ncf_modificado' => '',
                'retencion_isr' => $c->retencion_isr ?? 0,
                'retencion_itbis' => $c->retencion_itbis ?? 0,
            ];
        });

        return [
            'registros' => $registros,
            'total_monto' => $registros->sum('monto_facturado'),
            'total_itbis' => $registros->sum('itbis'),
            'total_impuestos' => $registros->sum('impuestos'),
            'total_general' => $registros->sum('total'),
            'cantidad' => $registros->count(),
            'periodo' => $periodo,
            'titulo' => 'Formato 606 - Compras',
        ];
    }

    private function tipoComprobanteLabel(?string $tipo): string
    {
        return match ($tipo) {
            'B01', '01' => 'Crédito Fiscal',
            'B02', '02' => 'Consumo',
            'B11', '11' => 'Consumo',
            'B12', '12' => 'Nota de Débito',
            'B13', '13' => 'Nota de Crédito',
            'B14', '14' => 'Regímenes Especiales',
            'B15', '15' => 'Gubernamental',
            'B16', '16' => 'Exportación',
            'E31' => 'e-CF Crédito Fiscal',
            'E32' => 'e-CF Consumo',
            'E33' => 'e-CF Nota Débito',
            'E34' => 'e-CF Nota Crédito',
            'E41' => 'e-CF Compras',
            'E44' => 'e-CF Reg. Especiales',
            'E45' => 'e-CF Gubernamental',
            'ecf' => 'e-CF',
            'sin_comprobante' => 'Sin Comprobante',
            'ticket' => 'Ticket',
            default => $tipo ?? 'Consumo',
        };
    }

    private function tipoIdentificacion(string $rnc): string
    {
        if (strlen($rnc) === 11 && in_array($rnc[0] ?? '', ['1', '4', '5'])) return '1'; // RNC
        if (strlen($rnc) === 9) return '1'; // RNC sin formato
        if (strlen($rnc) === 11) return '2'; // Cédula
        return '3'; // Pasaporte / Otro
    }

    private function toIso(string $text): string
    {
        return mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
    }

    public function resumen(Request $request)
    {
        $anio = $request->input('anio', now()->year);

        $ventasPorMes = Venta::selectRaw('MONTH(created_at) as mes, COUNT(*) as cantidad, SUM(total) as total, SUM(impuestos) as itbis')
            ->whereYear('created_at', $anio)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('mes');

        $comprasPorMes = Compra::selectRaw('MONTH(fecha) as mes, COUNT(*) as cantidad, SUM(total) as total, SUM(itbis_total) as itbis')
            ->whereYear('fecha', $anio)
            ->groupBy(DB::raw('MONTH(fecha)'))
            ->orderBy(DB::raw('MONTH(fecha)'))
            ->get()
            ->keyBy('mes');

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $v = $ventasPorMes->get($m);
            $c = $comprasPorMes->get($m);
            $meses[] = [
                'mes' => $m,
                'label' => Carbon::create()->month($m)->translatedFormat('F'),
                'ventas_cant' => $v?->cantidad ?? 0,
                'ventas_total' => $v?->total ?? 0,
                'ventas_itbis' => $v?->itbis ?? 0,
                'compras_cant' => $c?->cantidad ?? 0,
                'compras_total' => $c?->total ?? 0,
                'compras_itbis' => $c?->itbis ?? 0,
            ];
        }

        return view('reportes.resumen', compact('meses', 'anio'));
    }
}
