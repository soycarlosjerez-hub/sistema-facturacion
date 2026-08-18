<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Services\NotificationService;

class NotifyOrderStatusChanged
{
    public function __construct(private NotificationService $notifications) {}

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

        $this->notifications->notifyInstance(
            type: 'order_status_changed',
            category: 'order',
            title: 'Orden #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' → ' . ucfirst(str_replace('_', ' ', $to)),
            body: sprintf('Estado cambiado de "%s" a "%s"', $from, $to),
            extra: [
                'icon' => $icons[$to] ?? 'bi-arrow-right-circle',
                'color' => $colors[$to] ?? '#6366f1',
                'action_url' => route('ordenes.show', $order->id),
                'category_icon' => 'bi-list-ul',
                'category_label' => 'Órdenes',
                'verb' => 'cambió el estado de la orden',
            ],
            tenantId: $order->tenant_id,
            actor: $order->usuario,
        );
    }
}