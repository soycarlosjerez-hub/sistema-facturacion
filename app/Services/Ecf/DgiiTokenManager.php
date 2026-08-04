<?php

namespace App\Services\Ecf;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DgiiTokenManager
{
    private const TOKEN_CACHE_KEY = 'dgii_token_%s';

    private const TOKEN_TTL_MINUTES = 55;

    public function getToken(string $ambiente = null): ?string
    {
        $ambiente = $ambiente ?? config('dgii.ambiente', 'sandbox');

        $cacheKey = sprintf(self::TOKEN_CACHE_KEY, md5($ambiente));

        return Cache::remember($cacheKey, self::TOKEN_TTL_MINUTES * 60, function () use ($ambiente) {
            return $this->obtenerTokenDesdeDgii($ambiente);
        });
    }

    public function refreshToken(string $ambiente = null): ?string
    {
        $ambiente = $ambiente ?? config('dgii.ambiente', 'sandbox');
        $cacheKey = sprintf(self::TOKEN_CACHE_KEY, md5($ambiente));

        Cache::forget($cacheKey);

        return $this->obtenerTokenDesdeDgii($ambiente);
    }

    public function invalidateToken(string $ambiente = null): void
    {
        $ambiente = $ambiente ?? config('dgii.ambiente', 'sandbox');
        $cacheKey = sprintf(self::TOKEN_CACHE_KEY, md5($ambiente));
        Cache::forget($cacheKey);
    }

    private function obtenerTokenDesdeDgii(string $ambiente): ?string
    {
        $configAmbiente = config('dgii.ambientes.' . $ambiente);

        if (!$configAmbiente) {
            Log::error('e-CF: ambiente DGII no configurado', ['ambiente' => $ambiente]);
            return null;
        }

        if ($configAmbiente['cert_required'] ?? false) {
            return $this->autenticacionMutua($ambiente, $configAmbiente);
        }

        return $this->autenticacionApiKey($ambiente, $configAmbiente);
    }

    private function autenticacionMutua(string $ambiente, array $configAmbiente): ?string
    {
        $certConfig = config('dgii.certificates.' . $ambiente, []);

        if (empty($certConfig['client_cert_path']) || empty($certConfig['client_key_path'])) {
            Log::error('e-CF: credenciales mTLS no configuradas para autentificacion mutua', [
                'ambiente' => $ambiente,
            ]);
            return null;
        }

        $certPath = $certConfig['client_cert_path'];
        $keyPath = $certConfig['client_key_path'];
        $keyPass = $certConfig['client_key_pass'] ?? '';

        if (!file_exists($certPath) || !file_exists($keyPath)) {
            Log::error('e-CF: archivo de certificado mTLS no encontrado', [
                'cert_path' => $certPath,
                'key_path' => $keyPath,
            ]);
            return null;
        }

        $certContent = file_get_contents($certPath);
        $keyContent = file_get_contents($keyPath);

        if (!$certContent || !$keyContent) {
            Log::error('e-CF: no se pudieron leer los archivos de certificado mTLS');
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->withBody($certContent, 'application/x-pem-file')
                ->withOptions([
                    'ssl_cert' => $certPath,
                    'ssl_key' => $keyPath,
                    'verify' => config('dgii.ambientes.' . $ambiente . '.ca_bundle') ?? true,
                ])
                ->post($configAmbiente['api_url'] . '/auth/token');

            if (!$response->successful()) {
                Log::error('e-CF: fallo autentificacion mutua DGII', [
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $token = $data['access_token'] ?? $data['token'] ?? null;

            if ($token) {
                Log::info('e-CF: token obtenido exitosamente via mTLS', ['ambiente' => $ambiente]);
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('e-CF: error en autentificacion mutua DGII', [
                'error' => $e->getMessage(),
                'ambiente' => $ambiente,
            ]);
            return null;
        }
    }

    private function autenticacionApiKey(string $ambiente, array $configAmbiente): ?string
    {
        $apiKey = config('dgii.api_key.' . $ambiente) ?? config('dgii.api_key.default');

        if (!$apiKey) {
            Log::warning('e-CF: API key no configurada, retornando token simulado', ['ambiente' => $ambiente]);
            return $this->generarTokenSimulado($ambiente);
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($configAmbiente['api_url'] . '/auth/token');

            if (!$response->successful()) {
                Log::error('e-CF: fallo autentificacion API key DGII', [
                    'http_status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            $token = $data['access_token'] ?? $data['token'] ?? null;

            if ($token) {
                Log::info('e-CF: token obtenido exitosamente via API key', ['ambiente' => $ambiente]);
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('e-CF: error en autentificacion API key DGII', [
                'error' => $e->getMessage(),
                'ambiente' => $ambiente,
            ]);
            return null;
        }
    }

    private function generarTokenSimulado(string $ambiente): string
    {
        return 'SIM-TOKEN-' . strtoupper(bin2hex(random_bytes(16))) . '-' . $ambiente;
    }

    public function attachTokenToRequest($http, string $ambiente = null)
    {
        $token = $this->getToken($ambiente);

        if ($token) {
            return $http->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-DGII-Ambiente' => $ambiente ?? config('dgii.ambiente', 'sandbox'),
            ]);
        }

        return $http;
    }

    public function getAuthenticatedHttpClient(string $ambiente = null): \Illuminate\Http\Client\PendingRequest
    {
        $ambiente = $ambiente ?? config('dgii.ambiente', 'sandbox');
        $configAmbiente = config('dgii.ambientes.' . $ambiente);

        $http = Http::timeout(30)
            ->withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/xml',
            ]);

        if ($configAmbiente['cert_required'] ?? false) {
            $certConfig = config('dgii.certificates.' . $ambiente, []);
            if (!empty($certConfig['client_cert_path']) && !empty($certConfig['client_key_path'])) {
                $http = $http->withOptions([
                    'ssl_cert' => $certConfig['client_cert_path'],
                    'ssl_key' => $certConfig['client_key_path'],
                    'ssl_key_pass' => $certConfig['client_key_pass'] ?? '',
                    'verify' => config('dgii.ambientes.' . $ambiente . '.ca_bundle') ?? true,
                ]);
            }
        }

        $token = $this->getToken($ambiente);
        if ($token) {
            $http = $http->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ]);
        }

        return $http;
    }
}
