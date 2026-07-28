<?php

namespace App\Listeners;

use App\Models\InstanceErrorLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
                $tenantName = $errorLog->tenant->name ?? null;
                Mail::to($alertEmail)
                    ->queue(new \App\Mail\ErrorAlertMail(
                        level: $event->level,
                        title: $errorLog->title,
                        errorMessage: $event->message,
                        exceptionClass: $context['exception'] ?? null,
                        file: $context['file'] ?? null,
                        line: $context['line'] ?? null,
                        ipAddress: Request::ip(),
                        userAgent: Request::userAgent(),
                        context: $errorLog->context,
                        source: $errorLog->source,
                        createdAt: $errorLog->created_at->format('Y-m-d H:i:s'),
                        tenantName: $tenantName,
                    ))->onQueue('errors');
            }
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
