<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPaymentReceived implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(PaymentReceived $event): void
    {
        $pago = $event->pago;
        $venta = $pago->venta;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            UserNotification::createNotification(
                $userId,
                'payment_received',
                'Pago registrado #' . str_pad($venta->id, 5, '0', STR_PAD_LEFT),
                sprintf('Pago de RD$ %s vía %s', number_format($pago->monto, 2), ucfirst($pago->metodo_pago)),
                'payment',
                [
                    'icon' => 'bi-cash-coin',
                    'color' => '#8b5cf6',
                    'action_url' => route('ventas.show', $venta->id),
                    'category_icon' => 'bi-wallet2',
                    'category_label' => 'Pagos',
                ]
            );
        }
    }
}
