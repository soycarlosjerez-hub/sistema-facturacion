<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimiterMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $maxAttempts = '60', string $decayMinutes = '1'): Response
    {
        $key = 'ratelimiter:' . $request->ip() . ':' . $request->path();
        
        if (RateLimiter::tooManyAttempts($key, (int)$maxAttempts)) {
            return response()->json([
                'message' => 'Demasiadas peticiones. Intenta nuevamente en ' . $decayMinutes . ' minutos.',
                'retry_after' => (int)$decayMinutes * 60,
            ], 429);
        }

        RateLimiter::increment($key);

        return $next($request);
    }
}
