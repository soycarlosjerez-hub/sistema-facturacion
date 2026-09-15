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
        if (! $user) {
            $clientToken = $request->attributes->get('client_api_token');
            if ($clientToken && $clientToken->cliente) {
                return $next($request);
            }

            // Guest (login/register/welcome): dejar pasar, el scope hace fail-closed.
            // En API responder 401 JSON, en web dejar que 'auth' redirija.
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'No autenticado.'], 401);
            }

            return $next($request);
        }

        try {
            if ($user->hasRole('owner') || $user->hasRole('root')) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        if (! $user->business_instance_id) {
            // Sin instancia asignada: en API se deniega (401); en web se deja
            // pasar y el TenantScope fail-closed garantiza aislamiento
            // (no ve datos de ningún tenant). NUNCA aceptar tenant por header/query.
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'El usuario no tiene una instancia asignada.'], 401);
            }

            return $next($request);
        }

        $instance = $user->businessInstance;

        if ($instance && $instance->bloqueado) {
            // Las rutas de suscripción y la pantalla de bloqueo siempre accesibles
            // (coherente con CheckInstanceBlocked).
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                if ($request->routeIs('suscripcion.*') || $request->routeIs('instancia-bloqueada') || $request->is('logout')) {
                    return $next($request);
                }

                return redirect()->route('instancia-bloqueada')
                    ->with('error', 'Esta instancia ha sido bloqueada. '.($instance->motivo_bloqueo ?? ''));
            }

            return response()->json([
                'message' => 'Esta instancia ha sido bloqueada.',
                'motivo' => $instance->motivo_bloqueo ?? 'Sin especificar',
            ], 403);
        }

        return $next($request);
    }
}
