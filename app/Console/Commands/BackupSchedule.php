<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupSchedule extends Command
{
    protected $signature = 'backup:schedule';

    protected $description = 'Execute scheduled backups (daily/weekly/monthly)';

    public function handle(BackupService $backupService): int
    {
        $now = now();
        $backupType = 'daily';

        // Weekly backup on Monday
        if ($now->isMonday() && $now->day === 1 && $now->hour === 3) {
            $backupType = 'weekly';
        }

        // Monthly backup on the 1st
        if ($now->day === 1 && $now->hour === 2) {
            $backupType = 'monthly';
        }

        $this->info("Iniciando backup {$backupType}...");

        try {
            $backupService->createBackup($backupType);
            $this->info("Backup {$backupType} completado exitosamente.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('Scheduled backup failed', [
                'type' => $backupType,
                'message' => $e->getMessage(),
            ]);
            $this->error("El backup {$backupType} falló: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
