<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyOrderStatusChanged implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $from = $event->fromStatus;
        $to = $event->toStatus;

        $icons = [
            'confirmed' => 'bi-check-circle',
            'ready_for_pickup' => 'bi-basket',
            'shipped' => 'bi-truck',
        ];

        $colors = [
            'confirmed' => '#3b82f6',
            'ready_for_pickup' => '#10b981',
            'shipped' => '#f59e0b',
        ];

        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'order_status_changed',
                'Orden #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' → ' . ucfirst(str_replace('_', ' ', $to)),
                sprintf('Estado cambiado de "%s" a "%s"', $from, $to),
                'order',
                [
                    'icon' => $icons[$to] ?? 'bi-arrow-right-circle',
                    'color' => $colors[$to] ?? '#6366f1',
                    'action_url' => route('ordenes.show', $order->id),
                    'category_icon' => 'bi-list-ul',
                    'category_label' => 'Órdenes',
                ]
            );
        }
    }
}
