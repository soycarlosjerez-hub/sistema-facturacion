<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado.'], 401);
        }

        if (auth()->user()->business_instance_id === null) {
            return response()->json(['error' => 'Usuario sin instancia asignada.'], 403);
        }

        $instance = auth()->user()->businessInstance;
        if ($instance && $instance->bloqueado) {
            return response()->json(['error' => 'Instancia bloqueada.'], 403);
        }

        return $next($request);
    }
}
