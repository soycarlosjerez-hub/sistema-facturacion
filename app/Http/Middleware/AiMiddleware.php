<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiMiddleware
{
    public function handle(Request $request, Closure $next): JsonResponse|\Illuminate\Http\Response
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
