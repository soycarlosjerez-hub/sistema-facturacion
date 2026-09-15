<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRun extends Command
{
    protected $signature = 'backup:run {--type=automatico : manual o automatico}';

    protected $description = 'Realiza backup de la base de datos';

    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    public function handle(): int
    {
        $type = $this->option('type');
        $this->info("Iniciando backup tipo: {$type}...");

        try {
            $backup = $this->backupService->createBackup($type, null, false);

            if ($backup->status === 'completado') {
                $this->info("Backup completado: {$backup->filename} (".$backup->sizeForHumans().')');

                if ($type === 'automatico') {
                    $cleaned = \App\Models\Backup::cleanOldBackups();
                    if ($cleaned > 0) {
                        $this->info("Backups antiguos limpiados: {$cleaned}");
                    }
                }

                return Command::SUCCESS;
            } else {
                $this->error("Backup fallido: {$backup->notes}");

                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('Excepción al crear backup: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
