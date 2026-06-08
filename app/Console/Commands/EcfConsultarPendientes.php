<?php

namespace App\Console\Commands;

use App\Models\EcfDocumento;
use App\Services\Ecf\EcfService;
use Illuminate\Console\Command;

class EcfConsultarPendientes extends Command
{
    protected $signature = 'ecf:consultar-pendientes
        {--limite=50 : Cantidad máxima de documentos a consultar}
        {--solo-enviados : Solo documentos en estado "enviado"}
        {--fuerza : Re-consultar también los aprobados}';

    protected $description = 'Consulta el estado de e-CF pendientes con DGII';

    public function handle(EcfService $ecfService): int
    {
        $query = EcfDocumento::whereNotNull('track_id_dgii');

        if ($this->option('solo-enviados')) {
            $query->where('estado', 'enviado');
        } elseif (!$this->option('fuerza')) {
            $query->whereIn('estado', ['enviado', 'rechazado']);
        }

        $limite = (int) $this->option('limite');
        $documentos = $query->orderBy('fecha_envio', 'asc')->limit($limite)->get();

        if ($documentos->isEmpty()) {
            $this->info('No hay documentos pendientes por consultar.');
            return Command::SUCCESS;
        }

        $this->info("Consultando {$documentos->count()} documento(s)...");
        $bar = $this->output->createProgressBar($documentos->count());
        $bar->start();

        $resultados = ['aprobados' => 0, 'rechazados' => 0, 'errores' => 0, 'sin_cambio' => 0];

        foreach ($documentos as $ecf) {
            try {
                $before = $ecf->estado;
                $ecfService->consultarEstado($ecf);

                match (true) {
                    $ecf->estado !== $before && $ecf->estado === 'aprobado' => $resultados['aprobados']++,
                    $ecf->estado !== $before && $ecf->estado === 'rechazado' => $resultados['rechazados']++,
                    default => $resultados['sin_cambio']++,
                };

                $this->line(" {$ecf->encf}: {$before} → {$ecf->estado}");
            } catch (\Throwable $e) {
                $resultados['errores']++;
                $this->error(" {$ecf->encf}: ERROR - {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Aprobados', $resultados['aprobados']],
                ['Rechazados', $resultados['rechazados']],
                ['Sin cambio', $resultados['sin_cambio']],
                ['Errores', $resultados['errores']],
            ]
        );

        return Command::SUCCESS;
    }
}
