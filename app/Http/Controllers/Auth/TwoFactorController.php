<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FASecrets\Generation as SecretGeneration;

class TwoFactorController extends Controller
{
    /**
     * Instancia del generador/verificador TOTP.
     */
    protected Google2FA $google2fa;

    public function __construct(Google2FA $google2fa)
    {
        $this->google2fa = $google2fa;
    }

    /**
     * Mostrar vista de configuracion 2FA.
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return view('auth.two-factor', [
            'user'             => $user,
            'two_factor_enabled' => $user->two_factor_secret !== null,
        ]);
    }

    /**
     * Generar QR code y secreto para activar 2FA.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Si ya tiene 2FA activado, re-generar (reset)
        if ($user->two_factor_secret) {
            $user->two_factor_secret = null;
            $user->two_factor_recovery_codes = null;
            $user->two_factor_confirmed_at = null;
            $user->save();
        }

        // Generar secreto unico
        $secret = $this->google2fa->generateSecretKey(32);
        $user->two_factor_secret = $secret;
        $user->save();

        // Generar QR code (data URI SVG)
        $qrCodeUrl = $this->google2fa->getQRCodeInline(
            config('app.name', 'Sistema Facturacion'),
            $user->email,
            $secret
        );

        // Generar codigos de recuperacion (8 codigos de 8 digitos)
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = json_encode($recoveryCodes);
        $user->save();

        return response()->json([
            'success'       => true,
            'qr_code_url'   => $qrCodeUrl,
            'recovery_codes' => $recoveryCodes,
            'message'       => 'Escanee el codigo QR con su app autenticadora.',
        ]);
    }

    /**
     * Confirmar el codigo TOTP para activar 2FA.
     *
     * @return RedirectResponse
     */
    public function confirmEnable(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6|regex:/^\d+$/',
        ], [
            'code.required' => 'El codigo de verificacion es obligatorio.',
            'code.size'     => 'El codigo debe tener exactamente 6 digitos.',
            'code.regex'    => 'El codigo debe contener solo numeros.',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user->two_factor_secret) {
            return redirect()->route('two-factor.index')
                ->with('error', 'No hay un secreto 2FA configurado. Genere uno primero.');
        }

        // Verificar codigo TOTP
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Codigo de verificacion invalido. Intente nuevamente.'])
                ->withInput();
        }

        // Activar 2FA
        $user->two_factor_confirmed_at = now();
        $user->save();

        // Registrar evento de seguridad
        $this->logSecurityEvent($user, 'two_factor_enabled', [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('two-factor.index')
            ->with('success', 'Autenticacion de dos factores activada correctamente.');
    }

    /**
     * Desactivar 2FA.
     *
     * @return RedirectResponse
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Para desactivar 2FA debe confirmar su contraseña.',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar contraseña para validacion de seguridad
        if (!hash_equals($user->password, bcrypt($request->password))) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.'])
                ->withInput();
        }

        // Resetear 2FA
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        // Registrar evento de seguridad
        $this->logSecurityEvent($user, 'two_factor_disabled', [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('two-factor.index')
            ->with('success', 'Autenticacion de dos factores desactivada.');
    }

    /**
     * Mostrar codigos de recuperacion.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showRecovery(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verificar contraseña para mostrar codigos de recuperacion
        $validated = $request->validate([
            'password' => 'required|string',
        ], [
            'password.required' => 'Debe confirmar su contraseña para ver los codigos de recuperacion.',
        ]);

        if (!hash_equals($user->password, bcrypt($request->password))) {
            return response()->json([
                'success' => false,
                'error'   => 'Contraseña incorrecta.',
            ], 403);
        }

        if (!$user->two_factor_recovery_codes) {
            // Regenerar codigos de recuperacion si no existen
            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_recovery_codes = json_encode($recoveryCodes);
            $user->save();
        } else {
            $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        }

        return response()->json([
            'success'        => true,
            'recovery_codes' => $recoveryCodes,
            'message'        => 'Guarde estos codigos en un lugar seguro. Cada codigo solo puede usarse una vez.',
        ]);
    }

    /**
     * Regenerar codigos de recuperacion.
     *
     * @return RedirectResponse
     */
    public function regenerateRecovery(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        if (!hash_equals($user->password, bcrypt($request->password))) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.']);
        }

        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = json_encode($recoveryCodes);
        $user->save();

        return response()->json([
            'success'        => true,
            'recovery_codes' => $recoveryCodes,
            'message'        => 'Codigos de recuperacion regenerados correctamente.',
        ]);
    }

    /**
     * Verificar 2FA en un login. Usar desde un middleware o controlador de autenticacion.
     *
     * @return bool
     */
    public function verifyTwoFactor(Request $request): bool
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user || !$user->two_factor_secret) {
            return true; // No requiere 2FA
        }

        if ($request->two_factor_code) {
            // Verificar codigo TOTP
            if ($this->google2fa->verifyKey($user->two_factor_secret, $request->two_factor_code)) {
                return true;
            }

            // Verificar codigo de recuperacion
            if ($this->verifyRecoveryCode($user, $request->two_factor_code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si un codigo de recuperacion es valido.
     *
     * @return bool
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
     * Generar 8 codigos de recuperacion de 8 digitos.
     *
     * @return array<int, string>
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
        }

        return $codes;
    }

    /**
     * Registrar evento de seguridad en auditoria.
     */
    protected function logSecurityEvent(User $user, string $action, array $context = []): void
    {
        try {
            \App\Models\SecurityEventLog::create([
                'user_id'         => $user->id,
                'business_instance_id' => $user->business_instance_id,
                'action'          => $action,
                'description'     => "Evento 2FA: {$action}",
                'ip_address'      => $context['ip_address'] ?? null,
                'user_agent'      => $context['user_agent'] ?? null,
                'metadata'        => json_encode($context),
            ]);
        } catch (\Throwable $e) {
            // Si la tabla no existe, ignorar (fallback)
            \Log::warning('Security event log failed (table may not exist yet): ' . $e->getMessage());
        }
    }
}
