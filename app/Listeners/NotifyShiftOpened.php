<?php

namespace App\Listeners;

use App\Events\ShiftOpened;
use App\Services\NotificationService;

class NotifyShiftOpened
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(ShiftOpened $event): void
    {
        $sesion = $event->sesion;

        $this->notifications->notifyInstance(
            type: 'shift_opened',
            category: 'cash',
            title: 'Caja abierta: ' . $sesion->caja->nombre,
            body: sprintf('El usuario %s abrió la caja %s a las %s', $sesion->user->name, $sesion->caja->nombre, $sesion->fecha_apertura->format('H:i')),
            extra: [
                'icon' => 'bi-cash-stack',
                'color' => '#10b981',
                'action_url' => route('cajas.index'),
                'category_icon' => 'bi-cash-stack',
                'category_label' => 'Caja',
                'verb' => 'abrió la caja',
            ],
            tenantId: $sesion->tenant_id,
            actor: $sesion->user,
        );
    }
}