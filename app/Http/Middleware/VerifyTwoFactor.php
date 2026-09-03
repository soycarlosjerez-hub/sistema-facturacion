<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Excluir rutas públicas que no requieren autenticación
        $publicRoutes = [
            'login', 'logout', 'register',
            'two-factor.verify', 'two-factor.verify.submit',
            'password.reset', 'password.email', 'password.update',
        ];
        
        if (in_array($request->route()->getName() ?? '', $publicRoutes)) {
            return $next($request);
        }

        // Solo aplicar si hay un usuario autenticado
        if (!Auth::check()) {
            return $next($request);
        }

        /** @var User $user */
        $user = Auth::user();

        // Si no tiene 2FA activado, continuar
        if (!$user->two_factor_secret) {
            return $next($request);
        }

        // Si la ruta es la de verificación 2FA, permitir
        if ($request->routeIs('two-factor.*')) {
            return $next($request);
        }

        // Si es una petición de datos (AJAX/API), no usar session flash
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message'    => 'Autenticación de dos factores requerida.',
                'two_factor' => true,
            ], 419);
        }

        // Redirigir a verificación 2FA si no está confirmado
        if (!$request->session()->has('auth_two_factor_verified')) {
            return redirect()->route('two-factor.verify');
        }

        return $next($request);
    }

    /**
     * Marcar que el 2FA fue verificado exitosamente en la sesión.
     */
    public function markVerified(): void
    {
        session(['auth_two_factor_verified' => now()->addMinutes(config('auth.2fa_lifetime', 30))]);
    }

    /**
     * Verificar si el 2FA está verificado en sesión.
     */
    public function isVerified(): bool
    {
        if (!session()->has('auth_two_factor_verified')) {
            return false;
        }

        $expiresAt = session('auth_two_factor_verified');
        return now()->lessThanOrEqualTo(now()->parse($expiresAt));
    }
}
