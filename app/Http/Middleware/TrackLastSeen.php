<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackLastSeen
{
    /**
     * Actualiza last_seen_at del usuario autenticado cada 2 minutos como máximo
     * para no saturar la base de datos con writes en cada request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = "last_seen_user_{$user->id}";

            // Actualizar cada 2 minutos
            if (!Cache::has($cacheKey)) {
                $user->timestamps = false;
                $user->last_seen_at = now();
                $user->save();

                Cache::put($cacheKey, true, now()->addMinutes(2));
            }
        }

        return $next($request);
    }
}
