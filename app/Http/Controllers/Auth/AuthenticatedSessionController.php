<?php

namespace App\Http\Controllers\Auth;

use App\Auth\OwnerBootstrappedUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\OwnerBootstrapService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Determinar si es Owner Bootstrap
        $isBootstrap = $user instanceof OwnerBootstrappedUser;
        $userModel = $isBootstrap ? null : $user;

        session(['owner_bootstrap' => $isBootstrap]);

        if ($userModel) {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_at' => now(),
            ]);
        }

        if ($user) {
            // 1. Detect and set the active sucursal in the session
            if ($userModel && $userModel->sucursal_id) {
                session(['sucursal_id' => $userModel->sucursal_id]);
            } else {
                if (!$isBootstrap) {
                    $firstSucursal = \App\Models\Sucursal::first();
                    if ($firstSucursal) {
                        session(['sucursal_id' => $firstSucursal->id]);
                    }
                }
            }

            // 2. Load business type modules into session
            $tipoNegocio = null;
            if ($userModel && $userModel->businessInstance && $userModel->businessInstance->businessType) {
                $tipoNegocio = $userModel->businessInstance->businessType->slug;
            } elseif ($userModel && $userModel->businessType) {
                $tipoNegocio = $userModel->businessType->slug;
            } else {
                $tipoNegocio = 'restaurante'; // default/fallback
            }

            session(['business_type_slug' => $tipoNegocio]);
            if ($userModel && $userModel->business_instance_id) {
                session(['business_instance_id' => $userModel->business_instance_id]);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_at' => now(),
            ]);
        }

        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }
}
