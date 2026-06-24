<?php

namespace App\Listeners;

use App\Models\InstanceErrorLog;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
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

        InstanceErrorLog::create([
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
