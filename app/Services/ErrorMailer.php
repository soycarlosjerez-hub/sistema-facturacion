<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class ErrorMailer
{
    /**
     * Aplicar configuración SMTP global del owner antes de enviar correos de error.
     * SIEMPRE lee settings con tenant_id=NULL, nunca settings de tenant.
     *
     * @return array ['success' => bool, 'error' => string|null]
     */
    public static function applyGlobalSmtp(): array
    {
        try {
            $settings = SystemSetting::query()
                ->whereNull('tenant_id')
                ->pluck('value', 'key')
                ->toArray();

            if (empty($settings['mail_host'])) {
                return ['success' => false, 'error' => 'No hay configuración SMTP global en el sistema'];
            }

            $mailer = $settings['mail_mailer'] ?? 'smtp';
            if ($mailer === 'log') {
                $mailer = 'smtp';
            }

            Config::set('mail.default', $mailer);
            Config::set('mail.mailers.' . $mailer . '.host', $settings['mail_host'] ?? '');
            Config::set('mail.mailers.' . $mailer . '.port', (int)($settings['mail_port'] ?? 587));
            Config::set('mail.mailers.' . $mailer . '.username', $settings['mail_username'] ?? null);

            if (!empty($settings['mail_password'])) {
                try {
                    Config::set('mail.mailers.' . $mailer . '.password', Crypt::decryptString($settings['mail_password']));
                } catch (\Throwable $e) {
                    Config::set('mail.mailers.' . $mailer . '.password', null);
                }
            }

            $enc = ($settings['mail_encryption'] ?? 'null') !== 'null' ? $settings['mail_encryption'] : null;
            Config::set('mail.mailers.' . $mailer . '.encryption', $enc);
            Config::set('mail.from.address', $settings['mail_from_address'] ?? 'no-reply@facturacion.local');
            Config::set('mail.from.name', $settings['mail_from_name'] ?? config('app.name'));

            return ['success' => true, 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener el correo de destino para alertas de error.
     * Prioridad: error_alert_email del sistema > ERROR_ALERT_EMAIL del .env
     *
     * @return string|null
     */
    public static function getAlertEmail(): ?string
    {
        $email = SystemSetting::get('error_alert_email');
        if ($email) {
            return trim($email);
        }

        return env('ERROR_ALERT_EMAIL') ?: null;
    }
}
