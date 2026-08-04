<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use App\Models\EcfLogEnvio;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EcfRetryService
{
    public const MAX_RETRIES = 5;

    public const BACKOFF_BASE_SECONDS = 60;

    public const BACKOFF_MULTIPLIER = 2;

    public function obtenerDocumentosParaReenvio(): Collection
    {
        $pendientes = EcfDocumento::whereIn('estado', ['rechazado', 'enviado'])
            ->where('intentos_envio', '<', self::MAX_RETRIES)
            ->whereNull('xml_content')
            ->orWhere(function ($q) {
                $q->where('estado', 'rechazado')
                  ->where('intentos_envio', '<', self::MAX_RETRIES);
            })
            ->orWhere(function ($q) {
                $q->where('estado', 'enviado')
                  ->where('estado', '!=', 'aprobado')
                  ->whereNotNull('track_id_dgii')
                  ->where(function ($sq) {
                      $sq->whereNull('fecha_aprobacion')
                         ->orWhere('fecha_aprobacion', '<', now()->subMinutes(30));
                  });
            })
            ->orderBy('created_at')
            ->get();

        return $pendientes;
    }

    public function reenviarDocumento(EcfDocumento $ecf): array
    {
        DB::beginTransaction();
        try {
            if (empty($ecf->xml_content)) {
                $ecf = app(EcfService::class)->firmar($ecf);
            }

            $backoffSeconds = $this->calcularBackoff($ecf->intentos_envio);
            if ($backoffSeconds > 0) {
                Log::info('e-CF: reenvio con backoff', [
                    'ecf_id' => $ecf->id,
                    'intentos_previos' => $ecf->intentos_envio,
                    'segundos_espera' => $backoffSeconds,
                ]);
            }

            $ecf->increment('intentos_envio');
            $ecf->transicionarA('enviado');
            $ecf->save();

            $resultado = app(EcfService::class)->enviar($ecf);

            DB::commit();

            return [
                'success' => $resultado['success'],
                'estado' => $ecf->estado,
                'mensaje' => $resultado['mensaje'] ?? '',
                'intentos_totales' => $ecf->intentos_envio,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('e-CF: error en reenvio manual', [
                'ecf_id' => $ecf->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'estado' => $ecf->estado,
                'mensaje' => 'Error de reenvio: ' . $e->getMessage(),
                'intentos_totales' => $ecf->intentos_envio,
            ];
        }
    }

    public function reenviarMasivo(Collection $documentos): array
    {
        $exitosos = 0;
        $fallidos = 0;
        $saltados = 0;

        foreach ($documentos as $doc) {
            if (!$doc->puedeTransicionarA('enviado') && $doc->estado !== 'rechazado') {
                $saltados++;
                continue;
            }

            $resultado = $this->reenviarDocumento($doc);
            if ($resultado['success']) {
                $exitosos++;
            } else {
                $fallidos++;
            }
        }

        Log::info('e-CF: reenvio masivo completado', [
            'exitosos' => $exitosos,
            'fallidos' => $fallidos,
            'saltados' => $saltados,
        ]);

        return compact('exitosos', 'fallidos', 'saltados');
    }

    public function sincronizarEstadoConDgii(): array
    {
        $pendientes = EcfDocumento::where('estado', 'enviado')
            ->whereNotNull('track_id_dgii')
            ->where(function ($q) {
                $q->whereNull('fecha_aprobacion')
                  ->orWhere('fecha_aprobacion', '<', now()->subHours(1));
            })
            ->get();

        $actualizados = 0;
        $errores = 0;

        foreach ($pendientes as $ecf) {
            try {
                $ecf = app(EcfService::class)->consultarEstado($ecf);
                if ($ecf->estado === 'aprobado') {
                    $actualizados++;
                }
            } catch (\Throwable $e) {
                $errores++;
                Log::error('e-CF: error consultando estado DGII', [
                    'ecf_id' => $ecf->id,
                    'track_id' => $ecf->track_id_dgii,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'consultados' => $pendientes->count(),
            'actualizados' => $actualizados,
            'errores' => $errores,
        ];
    }

    public function reconciliarPeriodo(int $mes, int $anio): array
    {
        $inicio = \Carbon\Carbon::create($anio, $mes, 1)->startOfMonth();
        $fin = \Carbon\Carbon::create($anio, $mes, 1)->endOfMonth();

        $locales = EcfDocumento::whereBetween('fecha_emision', [$inicio, $fin])
            ->whereNotNull('track_id_dgii')
            ->get()
            ->keyBy('encf');

        $aprobadosLocales = EcfDocumento::whereBetween('fecha_emision', [$inicio, $fin])
            ->where('estado', 'aprobado')
            ->whereNotNull('track_id_dgii')
            ->count();

        $pendientesLocales = EcfDocumento::whereBetween('fecha_emision', [$inicio, $fin])
            ->whereIn('estado', ['enviado', 'rechazado'])
            ->whereNotNull('track_id_dgii')
            ->count();

        return [
            'periodo' => "{$anio}-{$mes}",
            'total_con_track' => $locales->count(),
            'aprobados_locales' => $aprobadosLocales,
            'pendientes_verificacion' => $pendientesLocales,
            'discrepancias' => $locales->filter(fn($d) => $d->estado !== 'aprobado' && $d->estado !== 'anulado')->pluck('encf')->toArray(),
        ];
    }

    public function marcarComoFallidoDefinitivamente(EcfDocumento $ecf): void
    {
        $ecf->transicionarA('rechazado');
        $ecf->mensaje_dgii = 'Maximo de reintentos alcanzado (' . self::MAX_RETRIES . ')';
        $ecf->save();

        Log::warning('e-CF: documento marcado como fallido definitivo', [
            'ecf_id' => $ecf->id,
            'encf' => $ecf->encf,
            'intentos' => $ecf->intentos_envio,
        ]);
    }

    private function calcularBackoff(int $intentosPrevios): int
    {
        if ($intentosPrevios <= 0) {
            return 0;
        }

        $seconds = self::BACKOFF_BASE_SECONDS * pow(self::BACKOFF_MULTIPLIER, $intentosPrevios - 1);
        return min($seconds, 3600);
    }
}
