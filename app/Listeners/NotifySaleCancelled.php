<?php

namespace App\Listeners;

use App\Events\SaleCancelled;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySaleCancelled implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(SaleCancelled $event): void
    {
        $venta = $event->venta;
        $motivo = $event->motivo;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'sale_cancelled',
                'Venta anulada #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                sprintf('Venta anulada: %s', $motivo),
                'sale',
                [
                    'icon' => 'bi-x-octagon',
                    'color' => '#ef4444',
                    'action_url' => route('ventas.show', $venta->id),
                    'category_icon' => 'bi-cart-x',
                    'category_label' => 'Ventas',
                ]
            );
        }
    }
}
