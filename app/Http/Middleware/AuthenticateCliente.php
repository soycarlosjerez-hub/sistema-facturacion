<?php

namespace App\Http\Middleware;

use App\Models\ClientApiToken;
use Closure;
use Illuminate\Http\Request;

class AuthenticateCliente
{
    public function handle(Request $request, Closure $next)
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json(['message' => 'Token no proporcionado.'], 401);
        }

        $hash = hash('sha256', $bearer);
        $token = ClientApiToken::with('cliente')->where('token', $hash)->first();

        if (!$token || !$token->cliente) {
            return response()->json(['message' => 'Token inválido.'], 401);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            return response()->json(['message' => 'Token expirado.'], 401);
        }

        $token->update(['last_used_at' => now()]);

        $request->attributes->set('client_api_token', $token);
        $request->setUserResolver(fn() => $token->cliente);

        return $next($request);
    }
}
