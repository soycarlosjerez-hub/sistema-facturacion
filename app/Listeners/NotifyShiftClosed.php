<?php

namespace App\Listeners;

use App\Events\ShiftClosed;
use App\Services\NotificationService;

class NotifyShiftClosed
{
    public function __construct(private NotificationService $notifications) {}

    public function handle(ShiftClosed $event): void
    {
        $sesion = $event->sesion;
        $totalTurno = $sesion->monto_inicial + $sesion->ventas_efectivo + $sesion->ventas_tarjeta + $sesion->ventas_transferencia;

        $this->notifications->notifyInstance(
            type: 'shift_closed',
            category: 'cash',
            title: 'Caja cerrada: ' . $sesion->caja->nombre,
            body: sprintf('El usuario %s cerró la caja %s. Total del turno: RD$ %s', $sesion->user->name, $sesion->caja->nombre, number_format($totalTurno, 2)),
            extra: [
                'icon' => 'bi-box-arrow-down',
                'color' => '#f59e0b',
                'action_url' => route('cajas.index'),
                'category_icon' => 'bi-cash-stack',
                'category_label' => 'Caja',
                'verb' => 'cerró la caja',
            ],
            tenantId: $sesion->tenant_id,
            actor: $sesion->user,
        );
    }
}