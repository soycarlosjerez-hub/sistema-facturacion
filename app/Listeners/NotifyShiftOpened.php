<?php

namespace App\Listeners;

use App\Events\ShiftOpened;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyShiftOpened implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct() {}

    public function handle(ShiftOpened $event): void
    {
        $sesion = $event->sesion;
        $userIds = \App\Models\User::whereIn('role', ['admin', 'admin-business', 'root', 'gerente'])
            ->pluck('id')
            ->toArray();

        foreach ($userIds as $userId) {
            if ($userId === $sesion->user_id) continue;
            UserNotification::createNotification(
                $userId,
                'shift_opened',
                'Caja abierta: ' . $sesion->caja->nombre,
                sprintf('El usuario %s abrió la caja %s a las %s', $sesion->user->name, $sesion->caja->nombre, $sesion->fecha_apertura->format('H:i')),
                'cash',
                [
                    'icon' => 'bi-cash-stack',
                    'color' => '#10b981',
                    'action_url' => route('cajas.index'),
                    'category_icon' => 'bi-cash-stack',
                    'category_label' => 'Caja',
                ]
            );
        }
    }
}
