<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ApiRequestLogger
{
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'api_key',
        'secret',
        'private_key',
        'public_key',
        'credit_card',
        'cvv',
        'card_number',
        'bank_account',
        'ssn',
        'social_security',
    ];

    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $response = $next($request);
        $durationMs = (int)((microtime(true) - $startTime) * 1000);

        try {
            $this->saveLog($request, $response, $durationMs);
        } catch (\Throwable $e) {
            Log::channel('daily')->error('[ApiRequestLogger] Failed to save log: ' . $e->getMessage());
        }

        return $response;
    }

    private function saveLog(Request $request, $response, int $durationMs): void
    {
        $user = Auth::guard('web')->user();

        $payload = [
            'business_instance_id' => $user?->business_instance_id,
            'user_id' => $user?->id,
            'method' => strtoupper($request->method()),
            'uri' => $request->fullUrl(),
            'query_string' => $request->getQueryString(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_headers' => $this->sanitizeHeaders($request->headers->all()),
            'request_body' => $this->sanitizeBody($request),
            'response_status' => $response->getStatusCode(),
            'response_time_ms' => $durationMs,
        ];

        \App\Models\ApiRequestLog::create($payload);
    }

    private function sanitizeHeaders(array $headers): array
    {
        $allowedKeys = [
            'content-type',
            'accept',
            'x-requested-with',
            'x-csrf-token',
            'origin',
            'referer',
            'accept-language',
            'accept-encoding',
            'cache-control',
            'connection',
            'upgrade-insecure-requests',
            'sec-fetch-site',
            'sec-fetch-mode',
            'sec-fetch-user',
            'sec-fetch-dest',
        ];

        $result = [];
        foreach ($headers as $key => $value) {
            $lowerKey = strtolower($key);
            if (!in_array($lowerKey, $allowedKeys)) {
                continue;
            }
            if (is_array($value)) {
                $result[$key] = implode(', ', $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function sanitizeBody(Request $request): ?array
    {
        if (in_array(strtoupper($request->method()), ['GET', 'HEAD', 'OPTIONS'])) {
            return null;
        }

        $input = $request->all();
        if (empty($input)) {
            return null;
        }

        return $this->sanitizeArray($input);
    }

    private function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if ($this->isSensitiveField($key)) {
                $sanitized[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    private function isSensitiveField(string $field): bool
    {
        $lowerField = strtolower($field);
        foreach (self::SENSITIVE_FIELDS as $sensitive) {
            if (str_contains($lowerField, $sensitive)) {
                return true;
            }
        }
        return false;
    }
}
