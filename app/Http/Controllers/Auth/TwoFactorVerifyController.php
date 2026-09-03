<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VerifyTwoFactor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorVerifyController extends Controller
{
    protected VerifyTwoFactor $twoFactor;

    public function __construct(VerifyTwoFactor $twoFactor)
    {
        $this->twoFactor = $twoFactor;
    }

    /**
     * Mostrar pantalla de verificacion 2FA.
     */
    public function show(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('auth.two-factor-verify', [
            'user' => $user,
        ]);
    }

    /**
     * Verificar el codigo 2FA del login.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'two_factor_code' => 'required|string|size:6|regex:/^\d+$/',
        ], [
            'two_factor_code.required' => 'El codigo de verificacion es obligatorio.',
            'two_factor_code.size'     => 'El codigo debe tener exactamente 6 digitos.',
            'two_factor_code.regex'    => 'El codigo debe contener solo numeros.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->two_factor_secret) {
            $this->twoFactor->markVerified();
            return $this->redirectToAuthenticatedRoute();
        }

        // Instancia del generador TOTP
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $code = $request->two_factor_code;

        // Verificar TOTP
        if ($google2fa->verifyKey($user->two_factor_secret, $code)) {
            // Eliminar codigo de sesion temporal
            $request->session()->remove('temp_auth_user_id');
            $this->twoFactor->markVerified();

            return $this->redirectToAuthenticatedRoute();
        }

        // Verificar codigo de recuperacion
        if ($this->verifyRecoveryCode($user, $code)) {
            $request->session()->remove('temp_auth_user_id');
            $this->twoFactor->markVerified();

            return $this->redirectToAuthenticatedRoute();
        }

        return back()->withErrors([
            'two_factor_code' => 'Código de verificación incorrecto. Intente nuevamente.',
        ])->withInput($request->only('two_factor_code'));
    }

    /**
     * Verificar si un codigo de recuperacion es valido.
     */
    protected function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $codes = json_decode($user->two_factor_recovery_codes, true);

        if (!in_array($code, $codes)) {
            return false;
        }

        // Eliminar el codigo usado (one-time)
        $codes = array_diff($codes, [$code]);
        $user->two_factor_recovery_codes = json_encode(array_values($codes));
        $user->save();

        return true;
    }

    /**
     * Desautenticar al usuario para volver a login.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirigir a ruta despues de autenticacion exitosa.
     */
    protected function redirectToAuthenticatedRoute(): RedirectResponse
    {
        return redirect()->intended(route('dashboard'));
    }
}
