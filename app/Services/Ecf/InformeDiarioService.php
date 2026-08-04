<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\SystemSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InformeDiarioService
{
    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_ENVIADO = 'enviado';
    public const ESTADO_ERROR = 'error';

    public function generarInformeParaFecha(\DateTimeInterface $fecha): Collection
    {
        $year = $fecha->format('Y');
        $month = $fecha->format('m');
        $day = $fecha->format('d');

        $inicioDia = Carbon\Carbon::parse($fecha)->startOfDay();
        $finDia = Carbon\Carbon::parse($fecha)->endOfDay();

        $empresa = SystemSetting::allCached();

        $documentos = EcfDocumento::where('fecha_emision', '>=', $inicioDia)
            ->where('fecha_emision', '<=', $finDia)
            ->where('estado', 'aprobado')
            ->whereNotNull('track_id_dgii')
            ->orderBy('fecha_emision')
            ->get();

        if ($documentos->isEmpty()) {
            return collect([]);
        }

        return $documentos->map(function (EcfDocumento $doc) use ($empresa) {
            $tipoEcf = $doc->tipo_ecf;
            $requiereRnc = in_array($tipoEcf, ['E31', 'E33', 'E34', 'E44', 'E45']);

            return [
                'encf' => $doc->encf,
                'tipo_ecf' => $tipoEcf,
                'fecha_emision' => $doc->fecha_emision->format('Y-m-d'),
                'hora_emision' => $doc->fecha_emision->format('H:i:s'),
                'monto_total' => number_format((float) $doc->monto_total, 2, '.', ''),
                'monto_gravado' => number_format((float) $doc->monto_gravado_total, 2, '.', ''),
                'monto_exento' => number_format((float) $doc->monto_exento_total, 2, '.', ''),
                'itbis' => number_format((float) $doc->itbis_total, 2, '.', ''),
                'rnc_emisor' => $empresa['empresa_rnc'] ?? '000000000',
                'razon_social_emisor' => $empresa['empresa_nombre'] ?? '',
                'rnc_comprador' => $requiereRnc ? ($doc->venta?->cliente?->rnc_cedula ?? '') : '',
                'razon_social_comprador' => $doc->venta?->cliente?->nombre ?? 'Consumidor Final',
                'estado' => $doc->estado,
                'track_id_dgii' => $doc->track_id_dgii,
            ];
        });
    }

    public function generarInformeCompletoMes(int $mes, int $anio): Collection
    {
        $inicio = Carbon\Carbon::create($anio, $mes, 1)->startOfMonth();
        $fin = Carbon\Carbon::create($anio, $mes, 1)->endOfMonth();

        $empresa = SystemSetting::allCached();

        $documentos = EcfDocumento::whereBetween('fecha_emision', [$inicio, $fin])
            ->where('estado', 'aprobado')
            ->whereNotNull('track_id_dgii')
            ->orderBy('fecha_emision')
            ->get();

        return $documentos->map(function (EcfDocumento $doc) use ($empresa) {
            $tipoEcf = $doc->tipo_ecf;
            $requiereRnc = in_array($tipoEcf, ['E31', 'E33', 'E34', 'E44', 'E45']);

            return [
                'encf' => $doc->encf,
                'tipo_ecf' => $tipoEcf,
                'fecha_emision' => $doc->fecha_emision->format('Y-m-d'),
                'hora_emision' => $doc->fecha_emision->format('H:i:s'),
                'monto_total' => number_format((float) $doc->monto_total, 2, '.', ''),
                'monto_gravado' => number_format((float) $doc->monto_gravado_total, 2, '.', ''),
                'monto_exento' => number_format((float) $doc->monto_exento_total, 2, '.', ''),
                'itbis' => number_format((float) $doc->itbis_total, 2, '.', ''),
                'rnc_emisor' => $empresa['empresa_rnc'] ?? '000000000',
                'razon_social_emisor' => $empresa['empresa_nombre'] ?? '',
                'rnc_comprador' => $requiereRnc ? ($doc->venta?->cliente?->rnc_cedula ?? '') : '',
                'razon_social_comprador' => $doc->venta?->cliente?->nombre ?? 'Consumidor Final',
                'estado' => $doc->estado,
                'track_id_dgii' => $doc->track_id_dgii,
            ];
        });
    }

    public function enviarInformeDiario(\DateTimeInterface $fecha): array
    {
        $documentos = $this->generarInformeParaFecha($fecha);

        if ($documentos->isEmpty()) {
            Log::info('e-CF: no hay documentos aprobados para informar', [
                'fecha' => $fecha->format('Y-m-d'),
            ]);
            return [
                'success' => true,
                'mensaje' => 'No hay documentos para informar en esta fecha',
                'documentos_count' => 0,
            ];
        }

        $resultado = app(DgiiConnector::class)->enviarInformeDiario($documentos->toArray());

        if ($resultado['success']) {
            EcfDocumento::where('fecha_emision', '>=', Carbon\Carbon::parse($fecha)->startOfDay())
                ->where('fecha_emision', '<=', Carbon\Carbon::parse($fecha)->endOfDay())
                ->where('estado', 'aprobado')
                ->update(['ultimo_informe_diario' => now()]);
        }

        Log::info('e-CF: informe diario procesado', [
            'fecha' => $fecha->format('Y-m-d'),
            'exitoso' => $resultado['success'],
            'documentos' => $documentos->count(),
            'track_id' => $resultado['track_id'] ?? null,
        ]);

        return array_merge($resultado, ['documentos_count' => $documentos->count()]);
    }

    public function enviarInformePeriodo(int $mes, int $anio): array
    {
        $documentos = $this->generarInformeCompletoMes($mes, $anio);

        if ($documentos->isEmpty()) {
            return [
                'success' => true,
                'mensaje' => 'No hay documentos para informar en este periodo',
                'documentos_count' => 0,
            ];
        }

        $resultado = app(DgiiConnector::class)->enviarInformeDiario($documentos->toArray());

        Log::info('e-CF: informe periodico procesado', [
            'periodo' => "{$anio}-{$mes}",
            'exitoso' => $resultado['success'],
            'documentos' => $documentos->count(),
        ]);

        return array_merge($resultado, ['documentos_count' => $documentos->count()]);
    }

    public function verificarInformesPendientes(): array
    {
        $hoy = now();
        $ayeri = clone $hoy;
        $ayeri->modify('-1 day');

        $diasAVerificar = collect(range(0, 6))->map(fn($i) => clone $hoy->modify("-{$i} days"));

        $informesPendientes = [];

        foreach ($diasAVerificar as $dia) {
            $inicioDia = (clone $dia)->startOfDay();
            $finDia = (clone $dia)->endOfDay();

            $documentosDelDia = EcfDocumento::where('fecha_emision', '>=', $inicioDia)
                ->where('fecha_emision', '<=', $finDia)
                ->where('estado', 'aprobado')
                ->whereNotNull('track_id_dgii')
                ->get();

            if ($documentosDelDia->isEmpty()) {
                continue;
            }

            $noInformados = $documentosDelDia->filter(function (EcfDocumento $doc) {
                return !$doc->ultimo_informe_diario || $doc->ultimo_informe_diario->lte($inicioDia);
            });

            if ($noInformados->isNotEmpty()) {
                $informesPendientes[] = [
                    'fecha' => $dia->format('Y-m-d'),
                    'total_documentos' => $documentosDelDia->count(),
                    'no_informados' => $noInformados->count(),
                    'encf_pendientes' => $noInformados->pluck('encf')->toArray(),
                ];
            }
        }

        return [
            'informes_pendientes' => $informesPendientes,
            'total_no_informados' => collect($informesPendientes)->sum('no_informados'),
        ];
    }

    public function obtenerResumenDiario(\DateTimeInterface $fecha): array
    {
        $inicioDia = Carbon\Carbon::parse($fecha)->startOfDay();
        $finDia = Carbon\Carbon::parse($fecha)->endOfDay();

        $stats = EcfDocumento::whereBetween('fecha_emision', [$inicioDia, $finDia])
            ->selectRaw('estado, COUNT(*) as cantidad, SUM(monto_total) as total_monto')
            ->groupBy('estado')
            ->get()
            ->keyBy('estado');

        $totalAprobados = data_get($stats, 'aprobado.cantidad', 0) ?? 0;
        $totalMontosAprobados = data_get($stats, 'aprobado.total_monto', 0) ?? 0;
        $totalRechazados = data_get($stats, 'rechazado.cantidad', 0) ?? 0;

        $porTipo = EcfDocumento::whereBetween('fecha_emision', [$inicioDia, $finDia])
            ->where('estado', 'aprobado')
            ->selectRaw('tipo_ecf, COUNT(*) as cantidad, SUM(monto_total) as total')
            ->groupBy('tipo_ecf')
            ->get()
            ->keyBy('tipo_ecf');

        return [
            'fecha' => $fecha->format('Y-m-d'),
            'total_aprobados' => $totalAprobados,
            'total_monto_aprobados' => round((float) $totalMontosAprobados, 2),
            'total_rechazados' => $totalRechazados,
            'por_tipo' => $porTipo->map(fn($row) => [
                'cantidad' => $row->cantidad,
                'total' => round((float) $row->total, 2),
            ]),
            'informado' => EcfDocumento::whereBetween('fecha_emision', [$inicioDia, $finDia])
                ->where('estado', 'aprobado')
                ->whereNotNull('ultimo_informe_diario')
                ->where('ultimo_informe_diario', '>', $inicioDia)
                ->exists(),
        ];
    }
}
