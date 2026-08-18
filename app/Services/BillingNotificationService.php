<?php

namespace App\Services;

use App\Mail\BienvenidaSuscripcionMail;
use App\Mail\PagoConfirmadoMail;
use App\Mail\RecordatorioSuscripcionMail;
use App\Mail\SuscripcionSuspendidaMail;
use App\Mail\TransferenciaRecibidaMail;
use App\Models\BusinessInstance;
use App\Models\InstanceNotificationSetting;
use App\Models\PagoInstancia;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillingNotificationService
{
    protected const CATEGORY = 'suscripcion';

    /**
     * IDs de los usuarios administradores de la instancia (rol 'admin' + owner).
     */
    public function adminUserIds(BusinessInstance $instance): array
    {
        return $this->adminUsers($instance)->pluck('id')->all();
    }

    public function adminEmails(BusinessInstance $instance): array
    {
        return $this->adminUsers($instance)
            ->pluck('email')
            ->filter()
            ->unique()
            ->all();
    }

    protected function adminUsers(BusinessInstance $instance)
    {
        return User::query()
            ->where('business_instance_id', $instance->id)
            ->where(function ($q) use ($instance) {
                $q->where('id', $instance->owner_user_id)
                    ->orWhereHas('instanceRole', fn ($r) => $r->where('name', 'admin'));
            })
            ->get();
    }

    protected function instanceAllows(BusinessInstance $instance, string $type): bool
    {
        try {
            $settings = InstanceNotificationSetting::forInstance($instance);
            $knownKeys = (new InstanceNotificationSetting())->getFillable();

            if (in_array($type, $knownKeys, true)) {
                return $settings->isEnabled($type);
            }
        } catch (\Throwable $e) {
            return true;
        }

        return true;
    }

    protected function notifyAdmins(
        BusinessInstance $instance,
        string $type,
        string $title,
        string $body,
        array $extra = [],
        ?\Closure $emailSender = null
    ): void {
        if (! $this->instanceAllows($instance, $type)) {
            return;
        }

        foreach ($this->adminUserIds($instance) as $userId) {
            UserNotification::createNotification(
                $userId,
                $type,
                $title,
                $body,
                self::CATEGORY,
                $extra,
                $instance->id
            );
        }

        if ($emailSender) {
            foreach ($this->adminEmails($instance) as $email) {
                try {
                    $emailSender($email);
                } catch (\Throwable $e) {
                    Log::warning("BillingNotificationService: no se pudo notificar a {$email}: " . $e->getMessage());
                }
            }
        }
    }

    protected function baseExtra(string $icon, string $color): array
    {
        return [
            'icon' => $icon,
            'color' => $color,
            'action_url' => route('suscripcion.index'),
            'category_icon' => 'bi-credit-card',
            'category_label' => 'Suscripción',
            'verb' => 'suscripción',
        ];
    }

    public function bienvenida(BusinessInstance $instance): void
    {
        $fecha = $instance->trial_ends_at?->format('d/m/Y') ?? '—';

        $this->notifyAdmins(
            $instance,
            'subscription_welcome',
            '¡Tu prueba de ' . $instance->trialDays() . ' días ha comenzado!',
            'Tu periodo de prueba termina el ' . $fecha . '. Realiza tu pago para continuar sin interrupciones.',
            $this->baseExtra('bi-rocket-takeoff', '#3b82f6'),
            fn ($to) => Mail::to($to)->send(new BienvenidaSuscripcionMail($instance))
        );
    }

    public function recordatorioPrueba(BusinessInstance $instance): void
    {
        $dias = $instance->diasPruebaRestantes();

        $this->notifyAdmins(
            $instance,
            'subscription_expiring',
            'Tu periodo de prueba termina pronto',
            "Quedan {$dias} día(s) de prueba. Paga tu suscripción para no interrumpir el servicio.",
            $this->baseExtra('bi-hourglass-split', '#f59e0b'),
            fn ($to) => Mail::to($to)->send(new RecordatorioSuscripcionMail($instance, 'Tu periodo de prueba está por terminar', $dias))
        );
    }

    public function recordatorioRenovacion(BusinessInstance $instance): void
    {
        $proximo = $instance->proximoPagoEsperado();
        $dias = $proximo ? max(0, (int) now()->startOfDay()->diffInDays($proximo->copy()->startOfDay())) : 0;
        $fecha = $proximo?->format('d/m/Y') ?? '—';

        $this->notifyAdmins(
            $instance,
            'subscription_expiring',
            'Tu suscripción está por vencer',
            "Tu suscripción vence el {$fecha}. Renueva tu mensualidad para continuar con todos los módulos activos.",
            $this->baseExtra('bi-calendar-event', '#f59e0b'),
            fn ($to) => Mail::to($to)->send(new RecordatorioSuscripcionMail($instance, 'Tu suscripción está por vencer', $dias))
        );
    }

    public function suspension(BusinessInstance $instance): void
    {
        $deuda = number_format($instance->deudaEstimada(), 2);

        $this->notifyAdmins(
            $instance,
            'subscription_suspended',
            'Suscripción suspendida por falta de pago',
            "Tu acceso ha sido restringido. Deuda estimada: RD$ {$deuda}. Realiza tu pago para desbloquear el sistema.",
            $this->baseExtra('bi-lock-fill', '#ef4444'),
            fn ($to) => Mail::to($to)->send(new SuscripcionSuspendidaMail($instance))
        );
    }

    public function transferenciaRecibida(BusinessInstance $instance, PagoInstancia $pago): void
    {
        $monto = number_format($pago->monto, 2);
        $referencia = $pago->referencia_externa ?: '—';

        $this->notifyAdmins(
            $instance,
            'payment_received',
            'Recibimos tu referencia de transferencia',
            "Referencia: {$referencia} por RD$ {$monto}. Será confirmada por nuestro equipo en breve.",
            $this->baseExtra('bi-cash-coin', '#10b981'),
            fn ($to) => Mail::to($to)->send(new TransferenciaRecibidaMail($instance, $pago))
        );
    }

    public function pagoConfirmado(BusinessInstance $instance, PagoInstancia $pago): void
    {
        $mes = $pago->mes_pagado?->format('m/Y') ?? '—';
        $monto = number_format($pago->monto, 2);

        $this->notifyAdmins(
            $instance,
            'payment_received',
            '¡Pago confirmado!',
            "Pago por RD$ {$monto} confirmado. Tu suscripción está activa hasta {$mes}.",
            $this->baseExtra('bi-check-circle-fill', '#10b981'),
            fn ($to) => Mail::to($to)->send(new PagoConfirmadoMail($instance, $pago))
        );
    }
}