<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CspMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Content Security Policy — permite recursos necesarios del sistema
        // Ajustar según las necesidades reales de cada módulo
        $csp = "default-src 'self'; "
             . "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com cdn.jsdelivr.net unpkg.com; "
             . "style-src 'self' 'unsafe-inline' cdnjs.cloudflare.com fonts.googleapis.com; "
             . "font-src 'self' cdnjs.cloudflare.com fonts.gstatic.com fonts.googleapis.com; "
             . "img-src 'self' data: blob:; "
             . "connect-src 'self' https://api.openai.com; "
             . "frame-src 'self' https://www.youtube.com https://player.vimeo.com; "
             . "object-src 'none'; "
             . "base-uri 'self'; "
             . "form-action 'self'; "
             . "frame-ancestors 'none'; "
             . "upgrade-insecure-requests";

        $response->headers->set('Content-Security-Policy', $csp, true);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
