<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiChatMethodGuard
{
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->path();

        if ($uri === 'ai/chat' && $request->method() !== 'POST') {
            Log::warning('AI Chat blocked non-POST request', [
                'ip' => $request->ip(),
                'referer' => $request->headers->get('referer', 'none'),
                'user_agent' => $request->headers->get('user-agent', 'unknown'),
                'method' => $request->method(),
                'timestamp' => now()->toIso8601String(),
            ]);
            return response('Method Not Allowed', 405)
                ->header('Allow', 'POST');
        }

        return $next($request);
    }
}
