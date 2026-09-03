<?php

namespace App\Jobs;

use App\Models\EcfDocumento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessEcfDocumentos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;
    public $tries = 5;
    public $backoff = [10, 30, 60, 120, 300];

    protected EcfDocumento $ecfDocumento;

    public function __construct(EcfDocumento $ecfDocumento)
    {
        $this->ecfDocumento = $ecfDocumento;
    }

    public function handle(): void
    {
        // TODO: Implementar procesamiento de e-CF con DGII
        // - Enviar XML a DGII
        // - Recibir confirmación
        // - Actualizar estado del e-CF
    }
}
