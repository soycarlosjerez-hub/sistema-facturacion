<?php

namespace App\Http\Middleware;

use App\Models\InstanceApiKey;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado.'], 401);
        }

        if (str_starts_with($token, 'iak_')) {
            return $this->authenticateWithApiKey($token, $request, $next);
        }

        return $this->authenticateWithSanctum($token, $request, $next);
    }

    private function authenticateWithApiKey(string $token, Request $request, Closure $next)
    {
        $hash = hash('sha256', $token);

        $apiKey = InstanceApiKey::where('key', $hash)
            ->where('is_active', true)
            ->first();

        if (!$apiKey) {
            return response()->json(['message' => 'API Key inválida o desactivada.'], 401);
        }

        $apiKey->update(['last_used_at' => now()]);

        $user = User::where('business_instance_id', $apiKey->business_instance_id)
            ->orderBy('id')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'No hay usuarios activos en la instancia.'], 401);
        }

        Auth::guard('web')->setUser($user);

        return $next($request);
    }

    private function authenticateWithSanctum(string $token, Request $request, Closure $next)
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return response()->json(['message' => 'Token expirado.'], 401);
        }

        $user = $accessToken->tokenable;

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 401);
        }

        $accessToken->update(['last_used_at' => now()]);

        Auth::guard('web')->setUser($user);

        return $next($request);
    }
}
