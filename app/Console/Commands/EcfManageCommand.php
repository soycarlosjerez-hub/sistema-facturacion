<?php

namespace App\Console\Commands;

use App\Services\Ecf\InformeDiarioService;
use App\Services\Ecf\EcfRetryService;
use App\Services\Ecf\EcfArchiveService;
use Illuminate\Console\Command;

class EcfManageCommand extends Command
{
    protected $signature = 'ecf:manage
        {action : enviar-informe | reenviar | sincronizar | archivar | verificar | resumen}
        {--fecha= : Fecha para enviar informe (YYYY-MM-DD)}
        {--mes= : Mes para informe periodico (MM)}
        {--anio= : Ano para informe periodico (YYYY)}
        {--limite=50 : Limite de documentos a procesar}
        {--forzar : Forzar procesamiento sin confirmacion}';

    protected $description = 'Gestion integral de e-CF: informes, reenvio, sincronizacion y archivado';

    public function handle(
        InformeDiarioService $informeService,
        EcfRetryService $retryService,
        EcfArchiveService $archiveService
    ): int {
        $action = $this->argument('action');

        return match ($action) {
            'enviar-informe' => $this->enviarInforme($informeService),
            'reenviar' => $this->reenviar($retryService),
            'sincronizar' => $this->sincronizar($retryService),
            'archivar' => $this->archivar($archiveService),
            'verificar' => $this->verificarPendientes($informeService),
            'resumen' => $this->mostrarResumen($informeService),
            default => Command::INVALID,
        };
    }

    private function enviarInforme(InformeDiarioService $service): int
    {
        $fechaStr = $this->option('fecha');

        if ($fechaStr) {
            $fecha = \Carbon\Carbon::parse($fechaStr);
            $this->info("Enviando informe diario para: {$fecha->format('Y-m-d')}");

            $resultado = $service->enviarInformeDiario($fecha);

            $this->line(" Documentos: {$resultado['documentos_count']}");
            $this->line(" Track ID: {$resultado['track_id'] ?? 'N/A'}");
            $this->line(" Mensaje: {$resultado['mensaje']}");

            return $resultado['success'] ? Command::SUCCESS : Command::FAILURE;
        }

        $mes = $this->option('mes');
        $anio = $this->option('anio');

        if ($mes && $anio) {
            $this->info("Enviando informe periodico: {$anio}-{$mes}");
            $resultado = $service->enviarInformePeriodo((int) $mes, (int) $anio);
            $this->line(" Documentos: {$resultado['documentos_count']}");
            return $resultado['success'] ? Command::SUCCESS : Command::FAILURE;
        }

        $ayeri = now()->subDay();
        $this->info("Enviando informe de ayer: {$ayeri->format('Y-m-d')}");
        $resultado = $service->enviarInformeDiario($ayeri);
        $this->line(" Documentos: {$resultado['documentos_count']}");
        return $resultado['success'] ? Command::SUCCESS : Command::FAILURE;
    }

    private function reenviar(EcfRetryService $service): int
    {
        $documentos = $service->obtenerDocumentosParaReenvio();

        if ($documentos->isEmpty()) {
            $this->info('No hay documentos pendientes de reenvio.');
            return Command::SUCCESS;
        }

        $this->info("Reenviando {$documentos->count()} documento(s)...");
        $bar = $this->output->createProgressBar($documentos->count());
        $bar->start();

        $resultados = $service->reenviarMasivo($documentos);

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Resultado', 'Cantidad'],
            [
                ['Exitosos', $resultados['exitosos']],
                ['Fallidos', $resultados['fallidos']],
                ['Saltados', $resultados['saltados']],
            ]
        );

        return $resultados['fallidos'] > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function sincronizar(EcfRetryService $service): int
    {
        $this->info('Sincronizando estados con DGII...');
        $resultado = $service->sincronizarEstadoConDgii();

        $this->table(
            ['Metrica', 'Valor'],
            [
                ['Consultados', $resultado['consultados']],
                ['Actualizados', $resultado['actualizados']],
                ['Errores', $resultado['errores']],
            ]
        );

        return $resultado['errores'] > 0 ? Command::WARNING : Command::SUCCESS;
    }

    private function archivar(EcfArchiveService $service): int
    {
        $fechaLimite = now()->subYears(EcfArchiveService::RETENCION_ANIOS);
        $this->info("Archivando documentos anteriores a: {$fechaLimite->format('Y-m-d')}");

        $resultado = $service->archivarDocumentosAnteriores($fechaLimite);
        $this->line(" {$resultado['mensaje']}");

        $estadisticas = $service->obtenerEstadisticasAlmacenamiento();
        $this->info("Almacenamiento: {$estadisticas['tamano_estimado_mb']} MB estimados");
        $this->info("Total documentos: {$estadisticas['total_documentos']}");
        $this->info("Archivados: {$estadisticas['archivados']}");
        $this->info("No archivados: {$estadisticas['no_archivados']}");

        return Command::SUCCESS;
    }

    private function verificarPendientes(InformeDiarioService $service): int
    {
        $this->info('Verificando informes pendientes...');
        $resultado = $service->verificarInformesPendientes();

        if (empty($resultado['informes_pendientes'])) {
            $this->info('Todos los informes diarios estan al dia.');
            return Command::SUCCESS;
        }

        $this->warn('Se encontraron informes pendientes:');
        foreach ($resultado['informes_pendientes'] as $pendiente) {
            $this->line(" {$pendiente['fecha']}: {$pendiente['no_informados']} documentos sin informar");
        }

        $this->line("");
        $this->line(" Total documentos no informados: {$resultado['total_no_informados']}");

        if ($this->option('forzar')) {
            $this->warn('Procesando informes pendientes automaticamente...');
            foreach ($resultado['informes_pendientes'] as $pendiente) {
                $fecha = \Carbon\Carbon::parse($pendiente['fecha']);
                $informeResult = $service->enviarInformeDiario($fecha);
                $this->line(" Informe {$pendiente['fecha']}: " . ($informeResult['success'] ? 'OK' : 'FAIL'));
            }
        }

        return Command::FAILURE;
    }

    private function mostrarResumen(InformeDiarioService $service): int
    {
        $resumen = $service->obtenerResumenDiario(now());

        $this->table(
            ['Metrica', 'Valor'],
            [
                ['Fecha', $resumen['fecha']],
                ['Aprobados hoy', $resumen['total_aprobados']],
                ['Monto total aprobados', '$' . number_format($resumen['total_monto_aprobados'], 2)],
                ['Rechazados hoy', $resumen['total_rechazados']],
                ['Ya informado', $resumen['informado'] ? 'Si' : 'No'],
            ]
        );

        if (!empty($resumen['por_tipo'])) {
            $this->info("\nPor tipo de comprobante:");
            foreach ($resumen['por_tipo'] as $tipo => $datos) {
                $this->line(" {$tipo}: {$datos['cantidad']} doc(s), RD$ {$datos['total']}");
            }
        }

        return Command::SUCCESS;
    }
}
