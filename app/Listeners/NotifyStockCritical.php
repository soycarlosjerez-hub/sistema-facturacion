<?php

namespace App\Listeners;

use App\Events\StockCritical;
use App\Services\NotificationService;

class NotifyStockCritical
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(StockCritical $event): void
    {
        $product = $event->product;

        $this->notifications->notifyInstance(
            type: 'stock_critical',
            category: 'inventory',
            title: 'Stock crítico: ' . $product->nombre,
            body: sprintf('Solo quedan %d unidades (mínimo: %d)', $event->currentStock, $product->stock_minimo ?? 5),
            extra: [
                'icon' => 'bi-exclamation-triangle',
                'color' => '#ef4444',
                'action_url' => route('productos.show', $product->id),
                'category_icon' => 'bi-box-seam',
                'category_label' => 'Inventario',
                'verb' => 'reportó stock crítico',
            ],
            tenantId: $product->tenant_id,
            actor: auth()->user(),
        );
    }
}