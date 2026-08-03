<?php

namespace App\Listeners;

use App\Events\SalePaid;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySalePaid implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(SalePaid $event): void
    {
        $sale = $event->sale;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'sale_paid',
                'Venta pagada #' . str_pad($sale->id, 5, '0', STR_PAD_LEFT),
                sprintf('Venta de RD$ %s marcada como pagada', number_format($sale->total, 2)),
                'payment',
                [
                    'icon' => 'bi-check-circle',
                    'color' => '#10b981',
                    'action_url' => route('ventas.show', $sale->id),
                    'category_icon' => 'bi-wallet2',
                    'category_label' => 'Pagos',
                ]
            );
        }
    }
}
