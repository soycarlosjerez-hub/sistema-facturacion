<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySaleCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(SaleCreated $event): void
    {
        $venta = $event->venta;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'sale_created',
                'Venta registrada #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                sprintf(
                    'Se registró una venta por RD$ %s a %s (%s)',
                    number_format($venta->total, 2),
                    $venta->cliente->nombre ?? 'Consumidor Final',
                    $venta->metodo_pago ?? 'efectivo'
                ),
                'sale',
                [
                    'icon' => 'bi-receipt',
                    'color' => '#10b981',
                    'action_url' => route('ventas.show', $venta->id),
                    'category_icon' => 'bi-cart-check',
                    'category_label' => 'Ventas',
                ]
            );
        }
    }
}
