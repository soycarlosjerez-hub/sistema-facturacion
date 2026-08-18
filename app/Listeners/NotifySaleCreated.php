<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Services\NotificationService;

class NotifySaleCreated
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(SaleCreated $event): void
    {
        $venta = $event->venta;

        $this->notifications->notifyInstance(
            type: 'sale_created',
            category: 'sale',
            title: 'Venta registrada #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
            body: sprintf(
                'Se registró una venta por RD$ %s a %s (%s)',
                number_format($venta->total, 2),
                $venta->cliente->nombre ?? 'Consumidor Final',
                $venta->metodo_pago ?? 'efectivo'
            ),
            extra: [
                'icon' => 'bi-receipt',
                'color' => '#10b981',
                'action_url' => route('ventas.show', $venta->id),
                'category_icon' => 'bi-cart-check',
                'category_label' => 'Ventas',
                'verb' => 'registró la venta',
            ],
            tenantId: $venta->tenant_id,
            actor: $venta->usuario,
        );
    }
}