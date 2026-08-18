<?php

namespace App\Listeners;

use App\Events\SaleCancelled;
use App\Services\NotificationService;

class NotifySaleCancelled
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(SaleCancelled $event): void
    {
        $venta = $event->venta;
        $motivo = $event->motivo;

        $this->notifications->notifyInstance(
            type: 'sale_cancelled',
            category: 'sale',
            title: 'Venta anulada #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
            body: sprintf('Venta anulada: %s', $motivo),
            extra: [
                'icon' => 'bi-x-octagon',
                'color' => '#ef4444',
                'action_url' => route('ventas.show', $venta->id),
                'category_icon' => 'bi-cart-x',
                'category_label' => 'Ventas',
                'verb' => 'anuló la venta',
            ],
            tenantId: $venta->tenant_id,
            actor: $venta->usuario,
        );
    }
}