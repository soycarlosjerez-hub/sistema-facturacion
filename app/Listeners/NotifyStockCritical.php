<?php

namespace App\Listeners;

use App\Events\StockCritical;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyStockCritical implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(StockCritical $event): void
    {
        $product = $event->product;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'stock_critical',
                'Stock crítico: ' . $product->nombre,
                sprintf('Solo quedan %d unidades (mínimo: %d)', $event->currentStock, $product->stock_minimo ?? 5),
                'inventory',
                [
                    'icon' => 'bi-exclamation-triangle',
                    'color' => '#ef4444',
                    'action_url' => route('productos.show', $product->id),
                    'category_icon' => 'bi-box-seam',
                    'category_label' => 'Inventario',
                ]
            );
        }
    }
}
