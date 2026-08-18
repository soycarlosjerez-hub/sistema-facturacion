<?php

namespace App\Listeners;

use App\Events\PurchaseCreated;
use App\Services\NotificationService;

class NotifyPurchaseCreated
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(PurchaseCreated $event): void
    {
        $compra = $event->compra;

        $this->notifications->notifyInstance(
            type: 'purchase_created',
            category: 'inventory',
            title: 'Compra registrada #' . str_pad($compra->id, 5, '0', STR_PAD_LEFT),
            body: sprintf('Compra por RD$ %s a %s', number_format($compra->total, 2), $compra->proveedor->nombre ?? 'Proveedor'),
            extra: [
                'icon' => 'bi-truck',
                'color' => '#3b82f6',
                'action_url' => route('compras.show', $compra->id),
                'category_icon' => 'bi-box-seam',
                'category_label' => 'Inventario',
                'verb' => 'registró la compra',
            ],
            tenantId: $compra->tenant_id,
            actor: $compra->user,
        );
    }
}