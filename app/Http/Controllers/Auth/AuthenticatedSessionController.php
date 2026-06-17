<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
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
        if ($user) {
            // 1. Detect and set the active sucursal in the session
            if ($user->sucursal_id) {
                session(['sucursal_id' => $user->sucursal_id]);
            } else {
                // If the user does not have a sucursal assigned, assign the first available one to avoid empty operational states
                $firstSucursal = \App\Models\Sucursal::first();
                if ($firstSucursal) {
                    session(['sucursal_id' => $firstSucursal->id]);
                    // Update user's sucursal_id in database so it is persistent
                    $user->update(['sucursal_id' => $firstSucursal->id]);
                }
            }

            // 2. Load business type modules into session to verify they are active/loaded
            $tipoNegocio = null;
            if ($user->businessInstance && $user->businessInstance->businessType) {
                $tipoNegocio = $user->businessInstance->businessType->slug;
            } elseif ($user->businessType) {
                $tipoNegocio = $user->businessType->slug;
            } else {
                $tipoNegocio = 'restaurante'; // default/fallback
            }
            
            session(['business_type_slug' => $tipoNegocio]);
            if ($user->business_instance_id) {
                session(['business_instance_id' => $user->business_instance_id]);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
