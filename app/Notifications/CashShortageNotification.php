<?php

namespace App\Notifications;

use App\Models\SesionCaja;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CashShortageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly SesionCaja $sesion,
        public readonly float $totalEsperado,
        public readonly bool $usedMasterKey = false,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $faltante = abs($this->sesion->descuadre);

        return [
            'sesion_id'      => $this->sesion->id,
            'caja_nombre'    => $this->sesion->caja->nombre ?? 'N/A',
            'usuario_nombre' => $this->sesion->user->name ?? 'N/A',
            'monto_declarado' => $this->sesion->monto_declarado,
            'total_esperado'  => $this->totalEsperado,
            'faltante'        => $faltante,
            'used_master_key' => $this->usedMasterKey,
            'message'         => $this->buildMessage(),
            'icon'            => 'bi-exclamation-triangle-fill',
            'color'           => '#ef4444',
        ];
    }

    protected function buildMessage(): string
    {
        $usuario = $this->sesion->user->name ?? 'N/A';
        $caja = $this->sesion->caja->nombre ?? 'N/A';
        $faltante = number_format(abs($this->sesion->descuadre), 2);
        $declarado = number_format($this->sesion->monto_declarado, 2);
        $esperado = number_format($this->totalEsperado, 2);

        $msg = "El usuario {$usuario} cerró la caja {$caja} con un faltante de RD\$ {$faltante}. "
             . "Declarado: RD\$ {$declarado}, esperado: RD\$ {$esperado}.";

        if ($this->usedMasterKey) {
            $msg .= " Se utilizó clave maestra de administrador para autorizar el cierre.";
        }

        return $msg;
    }
}
