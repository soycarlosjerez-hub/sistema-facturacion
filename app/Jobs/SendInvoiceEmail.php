<?php

namespace App\Jobs;

use App\Models\Venta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;
    public $backoff = [30, 60, 120];

    protected Venta $venta;
    protected ?string $emailCliente;

    public function __construct(Venta $venta, ?string $emailCliente = null)
    {
        $this->venta = $venta;
        $this->emailCliente = $emailCliente;
    }

    public function handle(): void
    {
        $email = $this->emailCliente ?? $this->venta->cliente?->email;
        
        if (!$email) {
            return;
        }

        // TODO: Implementar envío de factura por email
        // - Generar PDF de la factura
        // - Adjuntar al email
        // - Enviar con Laravel Mail
    }
}
