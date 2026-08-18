<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Services\NotificationService;

class NotifyPaymentReceived
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(PaymentReceived $event): void
    {
        $pago = $event->pago;
        $venta = $pago->venta;

        $this->notifications->notifyInstance(
            type: 'payment_received',
            category: 'payment',
            title: 'Pago registrado #' . str_pad($venta->id ?? $pago->id, 5, '0', STR_PAD_LEFT),
            body: sprintf('Pago de RD$ %s vía %s', number_format($pago->monto, 2), ucfirst($pago->metodo_pago)),
            extra: [
                'icon' => 'bi-cash-coin',
                'color' => '#8b5cf6',
                'action_url' => $venta ? route('ventas.show', $venta->id) : null,
                'category_icon' => 'bi-wallet2',
                'category_label' => 'Pagos',
                'verb' => 'registró un pago',
            ],
            tenantId: $pago->tenant_id,
            actor: $venta?->usuario,
        );
    }
}