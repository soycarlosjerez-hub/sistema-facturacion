<?php

namespace App\Listeners;

use App\Events\ShiftClosed;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyShiftClosed implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(ShiftClosed $event): void
    {
        $sesion = $event->sesion;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            if ($userId === $sesion->user_id) continue;
            UserNotification::createNotification(
                $userId,
                'shift_closed',
                'Caja cerrada: ' . $sesion->caja->nombre,
                sprintf('El usuario %s cerró la caja %s. Total del turno: RD$ %s', $sesion->user->name, $sesion->caja->nombre, number_format($sesion->monto_inicial + $sesion->ventas_efectivo + $sesion->ventas_tarjeta + $sesion->ventas_transferencia, 2)),
                'cash',
                [
                    'icon' => 'bi-box-arrow-down',
                    'color' => '#f59e0b',
                    'action_url' => route('cajas.index'),
                    'category_icon' => 'bi-cash-stack',
                    'category_label' => 'Caja',
                ]
            );
        }
    }
}
