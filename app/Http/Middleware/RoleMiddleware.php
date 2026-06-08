<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            abort(401);
        }

        $user = Auth::user();

        foreach ($roles as $rol) {
            if ($user->role === $rol || $user->hasRole($rol)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes el rol necesario para acceder.');
    }
}