<?php

namespace App\Listeners;

use App\Events\PurchaseCreated;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPurchaseCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(PurchaseCreated $event): void
    {
        $compra = $event->compra;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'purchase_created',
                'Compra registrada #' . str_pad($compra->id, 5, '0', STR_PAD_LEFT),
                sprintf('Compra por RD$ %s a %s', number_format($compra->total, 2), $compra->proveedor->nombre ?? 'Proveedor'),
                'inventory',
                [
                    'icon' => 'bi-truck',
                    'color' => '#3b82f6',
                    'action_url' => route('compras.show', $compra->id),
                    'category_icon' => 'bi-box-seam',
                    'category_label' => 'Inventario',
                ]
            );
        }
    }
}
