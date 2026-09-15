<?php

namespace App\Http\Middleware;

use App\Models\BusinessInstance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckInstanceAprobada
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return $next($request);
        }

        try {
            if ($user->hasRole('owner') || $user->hasRole('root')) {
                return $next($request);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $userInstance = BusinessInstance::where('owner_user_id', $user->id)
            ->orWhere('id', $user->business_instance_id)
            ->first();

        if (! $userInstance) {
            return $next($request);
        }

        if (! $userInstance->esAprobada()) {
            if ($request->is('solicitud-pendiente') || $request->is('solicitud-pendiente/*')) {
                return $next($request);
            }

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Tu negocio no está aprobado.',
                    'redirect' => route('solicitud.pendiente'),
                ], 403);
            }

            return redirect()->route('solicitud.pendiente')
                ->with('info', 'Tu solicitud para "'.$userInstance->nombre.'" está siendo revisada por nuestro equipo.');
        }

        return $next($request);
    }
}
