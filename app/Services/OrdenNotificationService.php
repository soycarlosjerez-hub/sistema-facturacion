<?php

namespace App\Services;

use App\Enums\OrdenTipo;
use App\Mail\OrdenConfirmadaMail;
use App\Mail\OrdenListaMail;
use App\Mail\OrdenEnCaminoMail;
use App\Models\Orden;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrdenNotificationService
{
    public function sendConfirmation(Orden $orden): void
    {
        if (!in_array($orden->tipo_orden, [OrdenTipo::Delivery->value, OrdenTipo::Pickup->value])) {
            return;
        }

        $cliente = $orden->cliente;
        if (!$cliente || !$cliente->email) return;

        try {
            $cc = SystemSetting::get('mail_from_address');
            Mail::to($cliente->email)
                ->cc($cc ?: null)
                ->send(new OrdenConfirmadaMail($orden));
        } catch (\Exception $e) {
            Log::error("Error enviando email confirmación orden #{$orden->id}: " . $e->getMessage());
        }
    }

    public function sendReadyForPickup(Orden $orden): void
    {
        if ($orden->tipo_orden !== OrdenTipo::Pickup->value) return;

        $cliente = $orden->cliente;
        if (!$cliente || !$cliente->email) return;

        try {
            Mail::to($cliente->email)->send(new OrdenListaMail($orden));
        } catch (\Exception $e) {
            Log::error("Error enviando email listo pickup #{$orden->id}: " . $e->getMessage());
        }
    }

    public function sendOnTheWay(Orden $orden): void
    {
        if ($orden->tipo_orden !== OrdenTipo::Delivery->value) return;

        $cliente = $orden->cliente;
        if (!$cliente || !$cliente->email) return;

        try {
            Mail::to($cliente->email)->send(new OrdenEnCaminoMail($orden));
        } catch (\Exception $e) {
            Log::error("Error enviando email en camino #{$orden->id}: " . $e->getMessage());
        }
    }
}
