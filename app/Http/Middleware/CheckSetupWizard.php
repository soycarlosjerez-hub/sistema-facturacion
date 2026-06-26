<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupWizard
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('owner') || $user->hasRole('root')) {
            return $next($request);
        }

        if ($request->routeIs('setup.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        $instance = $user->businessInstance;
        if (!$instance) {
            return $next($request);
        }

        if ($instance->setup_completed) {
            return $next($request);
        }

        $role = $user->instanceRole;
        if (!$role || $role->name !== 'admin') {
            return $next($request);
        }

        return redirect()->route('setup.wizard');
    }
}
