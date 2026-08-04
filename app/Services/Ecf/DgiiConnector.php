<?php

namespace App\Services\Ecf;

use App\Models\EcfDocumento;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DgiiConnector
{
    public function __construct(
        private DgiiTokenManager $tokenManager,
    ) {}

    public function enviar(EcfDocumento $ecf): array
    {
        $start = microtime(true);

        if (config('dgii.simular_dgii') || config('dgii.ambiente') === 'sandbox') {
            return $this->simularEnvio($ecf, $start);
        }

        try {
            $endpoint = $this->getEndpoint('recepcion');
            $ambiente = config('dgii.ambiente');

            $httpClient = $this->tokenManager->getAuthenticatedHttpClient($ambiente);

            $response = $httpClient
                ->withHeaders([
                    'Content-Type' => 'application/xml',
                    'Accept' => 'application/json',
                ])
                ->timeout(30)
                ->withBody($ecf->xml_content, 'application/xml')
                ->post($endpoint);

            $duration = (int) ((microtime(true) - $start) * 1000);
            $body = $response->body();
            $data = $response->json() ?? [];

            return [
                'success' => $response->successful(),
                'codigo_http' => $response->status(),
                'track_id' => $data['trackId'] ?? null,
                'estado' => $data['estado'] ?? 'desconocido',
                'mensaje' => $data['mensaje'] ?? 'Sin mensaje',
                'response' => $body,
                'duracion_ms' => $duration,
            ];
        } catch (\Throwable $e) {
            Log::error('e-CF: error enviando a DGII', ['error' => $e->getMessage(), 'ecf_id' => $ecf->id]);
            return [
                'success' => false,
                'codigo_http' => 0,
                'track_id' => null,
                'estado' => 'error',
                'mensaje' => 'Error de conexión: ' . $e->getMessage(),
                'response' => null,
                'duracion_ms' => (int) ((microtime(true) - $start) * 1000),
            ];
        }
    }

    public function consultarEstado(string $trackId, EcfDocumento $ecf): array
    {
        if (config('dgii.simular_dgii') || config('dgii.ambiente') === 'sandbox') {
            return $this->simularConsulta($trackId);
        }

        try {
            $endpoint = $this->getEndpoint('consulta') . '/' . $trackId;
            $ambiente = config('dgii.ambiente');

            $httpClient = $this->tokenManager->getAuthenticatedHttpClient($ambiente);

            $response = $httpClient
                ->timeout(20)
                ->get($endpoint);

            $data = $response->json() ?? [];

            return [
                'success' => $response->successful(),
                'estado' => $data['estado'] ?? 'desconocido',
                'mensaje' => $data['mensaje'] ?? '',
                'codigo_http' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'estado' => 'error',
                'mensaje' => $e->getMessage(),
                'codigo_http' => 0,
            ];
        }
    }

    public function enviarInformeDiario(array $documentos): array
    {
        if (config('dgii.simular_dgii') || config('dgii.ambiente') === 'sandbox') {
            return $this->simularEnvioInforme($documentos);
        }

        try {
            $endpoint = $this->getEndpoint('informe-diario');
            $ambiente = config('dgii.ambiente');

            $httpClient = $this->tokenManager->getAuthenticatedHttpClient($ambiente);

            $payload = json_encode([
                'fecha' => data_get($documentos, '0.fecha_emision', now()->format('Y-m-d')),
                'empresa_rnc' => data_get($documentos, '0.rnc_emisor', ''),
                'documentos' => collect($documentos)->map(fn($doc) => [
                    'encf' => $doc['encf'] ?? '',
                    'tipo_ecf' => $doc['tipo_ecf'] ?? '',
                    'fecha_emision' => $doc['fecha_emision'] ?? '',
                    'monto_total' => $doc['monto_total'] ?? 0,
                    'estado' => $doc['estado'] ?? 'aprobado',
                ])->toArray(),
            ]);

            $response = $httpClient
                ->timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($endpoint, ['data' => $payload]);

            $data = $response->json() ?? [];

            return [
                'success' => $response->successful(),
                'codigo_http' => $response->status(),
                'track_id' => $data['trackId'] ?? $data['track_id'] ?? null,
                'estado' => $data['estado'] ?? 'desconocido',
                'mensaje' => $data['mensaje'] ?? 'Sin mensaje',
                'response' => $response->body(),
                'duracion_ms' => 0,
            ];
        } catch (\Throwable $e) {
            Log::error('e-CF: error enviando informe diario a DGII', [
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'codigo_http' => 0,
                'track_id' => null,
                'estado' => 'error',
                'mensaje' => 'Error de conexión: ' . $e->getMessage(),
                'response' => null,
                'duracion_ms' => 0,
            ];
        }
    }

    private function getEndpoint(string $tipo): string
    {
        $base = rtrim(config('dgii.ambientes.' . config('dgii.ambiente') . '.api_url'), '/');
        return match ($tipo) {
            'recepcion' => $base . '/recepcion-ecf',
            'consulta' => $base . '/consulta-estado',
            'informe-diario' => $base . '/informe-diario',
            default => $base,
        };
    }

    private function simularEnvio(EcfDocumento $ecf, float $start): array
    {
        $prob = config('dgii.probabilidad_aprobacion_sim', 0.85);
        $approved = mt_rand() / mt_getrandmax() < $prob;
        $trackId = 'TRK-' . strtoupper(bin2hex(random_bytes(8)));
        $duration = mt_rand(150, 800);

        usleep(200000);

        if ($approved) {
            return [
                'success' => true,
                'codigo_http' => 200,
                'track_id' => $trackId,
                'estado' => 'aprobado',
                'mensaje' => 'e-CF recibido y aceptado por DGII (simulación)',
                'response' => json_encode([
                    'trackId' => $trackId,
                    'estado' => 'ACEPTADO',
                    'codigoSeguridad' => $ecf->codigo_seguridad,
                ]),
                'duracion_ms' => $duration,
            ];
        }

        $rejectionReasons = [
            'RNC del comprador no válido',
            'Monto total no coincide con la suma de líneas',
            'ITBIS calculado incorrectamente',
            'Secuencia de e-CF vencida',
            'Firma digital no válida',
        ];
        $reason = $rejectionReasons[array_rand($rejectionReasons)];

        return [
            'success' => false,
            'codigo_http' => 422,
            'track_id' => $trackId,
            'estado' => 'rechazado',
            'mensaje' => $reason,
            'response' => json_encode([
                'trackId' => $trackId,
                'estado' => 'RECHAZADO',
                'errores' => [$reason],
            ]),
            'duracion_ms' => $duration,
        ];
    }

    private function simularConsulta(string $trackId): array
    {
        usleep(100000);
        return [
            'success' => true,
            'estado' => 'aprobado',
            'mensaje' => 'e-CF aprobado en DGII (simulación)',
            'codigo_http' => 200,
            'track_id' => $trackId,
        ];
    }

    private function simularEnvioInforme(array $documentos): array
    {
        $count = count($documentos);
        return [
            'success' => true,
            'codigo_http' => 200,
            'track_id' => 'TRK-INFORME-' . strtoupper(bin2hex(random_bytes(8))),
            'estado' => 'aceptado',
            'mensaje' => "Informe diario de {$count} documentos procesado (simulación)",
            'response' => json_encode([
                'trackId' => 'TRK-INFORME-' . bin2hex(random_bytes(8)),
                'estado' => 'ACEPTADO',
                'documentos_procesados' => $count,
            ]),
            'duracion_ms' => mt_rand(300, 1500),
        ];
    }
}
