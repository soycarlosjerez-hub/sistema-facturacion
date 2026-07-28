<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;

class TenantMailConfig
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('owner') || $user->hasRole('root')) {
            return $next($request);
        }

        $tenantId = $user->business_instance_id ?? null;

        if (!$tenantId) {
            return $next($request);
        }

        try {
            $settings = \App\Models\SystemSetting::query()
                ->where('tenant_id', $tenantId)
                ->pluck('value', 'key')
                ->toArray();

            if (!empty($settings['mail_host'])) {
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
            }
        } catch (\Throwable $e) {
            // Fail silently
        }

        return $next($request);
    }
}
