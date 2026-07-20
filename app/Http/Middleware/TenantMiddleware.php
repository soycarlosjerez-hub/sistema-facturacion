<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Client token auth — no Auth::user, pero ya pasó AuthenticateApiKey
        if (!$user) {
            $clientToken = $request->attributes->get('client_api_token');
            if ($clientToken && $clientToken->cliente) {
                return $next($request);
            }
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if ($user->hasRole('owner') || $user->hasRole('root')) {
            return $next($request);
        }

        if (!$user->business_instance_id) {
            return response()->json(['message' => 'El usuario no tiene una instancia asignada.'], 401);
        }

        $instance = $user->businessInstance;

        if ($instance && $instance->bloqueado) {
            return response()->json([
                'message' => 'Esta instancia ha sido bloqueada.',
                'motivo' => $instance->motivo_bloqueo ?? 'Sin especificar',
            ], 403);
        }

        return $next($request);
    }
}
