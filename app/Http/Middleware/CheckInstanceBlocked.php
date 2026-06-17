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

        if ($user->business_instance_id) {
            $instance = $user->businessInstance;
            if ($instance && $instance->bloqueado) {
                return redirect()->route('instancia-bloqueada')
                    ->with('error', 'Esta instancia ha sido bloqueada. Motivo: ' . ($instance->motivo_bloqueo ?? 'Sin especificar'));
            }
        }

        return $next($request);
    }
}
