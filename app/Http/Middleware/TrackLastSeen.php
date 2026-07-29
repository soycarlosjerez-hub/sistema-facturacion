<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\UserActivityLog;

class TrackLastSeen
{
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

            // Log de actividad cada 5 minutos
            $logCacheKey = "activity_log_user_{$user->id}";
            if (!Cache::has($logCacheKey)) {
                UserActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'page_view',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logged_at' => now(),
                ]);

                Cache::put($logCacheKey, true, now()->addMinutes(5));
            }
        }

        return $next($request);
    }
}
