<?php

namespace App\Listeners;

use App\Events\SalePaid;
use App\Services\NotificationService;

class NotifySalePaid
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(SalePaid $event): void
    {
        $venta = $event->venta;

        $this->notifications->notifyInstance(
            type: 'sale_paid',
            category: 'payment',
            title: 'Venta pagada #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
            body: sprintf('Venta de RD$ %s marcada como pagada', number_format($venta->total, 2)),
            extra: [
                'icon' => 'bi-check-circle',
                'color' => '#10b981',
                'action_url' => route('ventas.show', $venta->id),
                'category_icon' => 'bi-wallet2',
                'category_label' => 'Pagos',
                'verb' => 'marcó como pagada la venta',
            ],
            tenantId: $venta->tenant_id,
            actor: $venta->usuario,
        );
    }
}