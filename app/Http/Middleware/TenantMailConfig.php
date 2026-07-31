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

        $globalSettings = null;

        try {
            $globalSettings = \App\Models\SystemSetting::query()
                ->whereNull('tenant_id')
                ->pluck('valor', 'clave')
                ->toArray();

            if (!empty($globalSettings['mail_host'])) {
                $mailer = $globalSettings['mail_mailer'] ?? 'smtp';
                if ($mailer === 'log') {
                    $mailer = 'smtp';
                }

                Config::set('mail.default', $mailer);
                Config::set('mail.mailers.' . $mailer . '.host', $globalSettings['mail_host'] ?? '');
                Config::set('mail.mailers.' . $mailer . '.port', (int)($globalSettings['mail_port'] ?? 587));
                Config::set('mail.mailers.' . $mailer . '.username', $globalSettings['mail_username'] ?? null);

                if (!empty($globalSettings['mail_password'])) {
                    try {
                        Config::set('mail.mailers.' . $mailer . '.password', Crypt::decryptString($globalSettings['mail_password']));
                    } catch (\Throwable $e) {
                        Config::set('mail.mailers.' . $mailer . '.password', null);
                    }
                }

                $enc = ($globalSettings['mail_encryption'] ?? 'null') !== 'null' ? $globalSettings['mail_encryption'] : null;
                Config::set('mail.mailers.' . $mailer . '.encryption', $enc);
                Config::set('mail.from.address', $globalSettings['mail_from_address'] ?? 'no-reply@facturacion.local');
                Config::set('mail.from.name', $globalSettings['mail_from_name'] ?? config('app.name'));
            }
        } catch (\Throwable $e) {
            // Ignore global settings errors
        }

        if ($user->hasRole('owner') || $user->hasRole('root')) {
            return $next($request);
        }

        $tenantId = $user->business_instance_id ?? null;

        if (!$tenantId) {
            return $next($request);
        }

        try {
            $tenantSettings = \App\Models\SystemSetting::query()
                ->where('tenant_id', $tenantId)
                ->pluck('valor', 'clave')
                ->toArray();

            if (!empty($tenantSettings['mail_host'])) {
                $mailer = $tenantSettings['mail_mailer'] ?? 'smtp';
                if ($mailer === 'log') {
                    $mailer = 'smtp';
                }

                Config::set('mail.default', $mailer);
                Config::set('mail.mailers.' . $mailer . '.host', $tenantSettings['mail_host'] ?? '');
                Config::set('mail.mailers.' . $mailer . '.port', (int)($tenantSettings['mail_port'] ?? 587));
                Config::set('mail.mailers.' . $mailer . '.username', $tenantSettings['mail_username'] ?? null);

                if (!empty($tenantSettings['mail_password'])) {
                    try {
                        Config::set('mail.mailers.' . $mailer . '.password', Crypt::decryptString($tenantSettings['mail_password']));
                    } catch (\Throwable $e) {
                        Config::set('mail.mailers.' . $mailer . '.password', null);
                    }
                }

                $enc = ($tenantSettings['mail_encryption'] ?? 'null') !== 'null' ? $tenantSettings['mail_encryption'] : null;
                Config::set('mail.mailers.' . $mailer . '.encryption', $enc);
                Config::set('mail.from.address', $tenantSettings['mail_from_address'] ?? 'no-reply@facturacion.local');
                Config::set('mail.from.name', $tenantSettings['mail_from_name'] ?? config('app.name'));
            }
        } catch (\Throwable $e) {
            // Ignore tenant settings errors
        }

        return $next($request);
    }
}
