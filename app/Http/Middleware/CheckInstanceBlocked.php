<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInstanceBlocked
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->hasRole('owner') || $user->hasRole('root')) {
            return $next($request);
        }

        // Las rutas de suscripción y la pantalla de bloqueo siempre deben ser accesibles.
        if ($request->routeIs('suscripcion.*') || $request->routeIs('instancia-bloqueada')) {
            return $next($request);
        }

        if ($user->business_instance_id) {
            $instance = $user->businessInstance;
            if ($instance && $instance->bloqueado) {
                return redirect()->route('instancia-bloqueada')
                    ->with('error', 'Esta instancia ha sido bloqueada por falta de pago. Motivo: ' . ($instance->motivo_bloqueo ?? 'Suscripción vencida'));
            }
        }

        return $next($request);
    }
}
