<?php

namespace App\Listeners;

use App\Models\InstanceErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\MessageLogged;

class LogErrorToDatabase
{
    public function handle(MessageLogged $event): void
    {
        if (!in_array($event->level, ['error', 'critical', 'warning'])) {
            return;
        }

        $context = $event->context;
        $tenantId = $context['tenant_id'] ?? Auth::user()?->business_instance_id ?? null;

        $title = mb_substr($event->message, 0, 255);

        $errorLog = InstanceErrorLog::create([
            'tenant_id' => $tenantId,
            'level' => $event->level,
            'title' => $title,
            'message' => $event->message,
            'context' => $this->cleanContext($context),
            'source' => $context['error_source'] ?? 'log',
            'user_id' => Auth::id(),
            'file' => $context['file'] ?? null,
            'line' => $context['line'] ?? null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);

        $alertEmail = config('app.error_alert_email', env('ERROR_ALERT_EMAIL'));
        if ($alertEmail) {
            $cacheKey = 'error_alert_log:' . md5($event->level . $event->message);
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, true, 300);
                $this->sendAlertWithInstanceSmtp($alertEmail, $errorLog, $event);
            }
        }
    }

    protected function sendAlertWithInstanceSmtp(string $to, InstanceErrorLog $errorLog, MessageLogged $event): void
    {
        $originalMailer = config('mail.default');
        $originalConfig = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'encryption' => config('mail.mailers.smtp.encryption'),
        ];

        $this->applyInstanceSmtp($errorLog->tenant_id);

        try {
            $tenantName = $errorLog->tenant->name ?? null;
            Mail::to($to)
                ->queue(new \App\Mail\ErrorAlertMail(
                    level: $event->level,
                    title: $errorLog->title,
                    errorMessage: $event->message,
                    exceptionClass: $event->context['exception'] ?? null,
                    file: $event->context['file'] ?? null,
                    line: $event->context['line'] ?? null,
                    ipAddress: Request::ip(),
                    userAgent: Request::userAgent(),
                    context: $errorLog->context,
                    source: $errorLog->source,
                    createdAt: $errorLog->created_at->format('Y-m-d H:i:s'),
                    tenantName: $tenantName,
                ))->onQueue('errors');
        } finally {
            config(['mail.default' => $originalMailer]);
            config(['mail.mailers.smtp.host' => $originalConfig['host']]);
            config(['mail.mailers.smtp.port' => $originalConfig['port']]);
            config(['mail.mailers.smtp.username' => $originalConfig['username']]);
            config(['mail.mailers.smtp.password' => $originalConfig['password']]);
            config(['mail.mailers.smtp.encryption' => $originalConfig['encryption']]);
        }
    }

    protected function applyInstanceSmtp(?int $tenantId): void
    {
        if (!$tenantId) {
            return;
        }

        try {
            $settings = \App\Models\SystemSetting::query()
                ->where('tenant_id', $tenantId)
                ->pluck('value', 'key')
                ->toArray();

            if (empty($settings['mail_host'])) {
                return;
            }

            $mailer = $settings['mail_mailer'] ?? 'smtp';
            $host = $settings['mail_host'] ?? null;
            $port = (int)($settings['mail_port'] ?? 587);
            $username = $settings['mail_username'] ?? null;
            $password = null;
            if (!empty($settings['mail_password'])) {
                try {
                    $password = Crypt::decryptString($settings['mail_password']);
                } catch (\Throwable $e) {
                    $password = null;
                }
            }
            $encryption = ($settings['mail_encryption'] ?? 'null') !== 'null' ? $settings['mail_encryption'] : null;

            config(['mail.default' => $mailer]);
            config(['mail.mailers.' . $mailer . '.host' => $host]);
            config(['mail.mailers.' . $mailer . '.port' => $port]);
            config(['mail.mailers.' . $mailer . '.username' => $username]);
            config(['mail.mailers.' . $mailer . '.password' => $password]);
            config(['mail.mailers.' . $mailer . '.encryption' => $encryption]);
        } catch (\Throwable $e) {
            // Fail silently, fall back to original config
        }
    }

    protected function cleanContext(array $context): array
    {
        unset($context['tenant_id'], $context['error_source']);

        foreach ($context as $key => $value) {
            if (is_object($value)) {
                $context[$key] = class_basename($value);
            }
        }

        return $context;
    }
}
