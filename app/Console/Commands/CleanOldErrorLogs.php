<?php

namespace App\Console\Commands;

use App\Models\InstanceErrorLog;
use Illuminate\Console\Command;

class CleanOldErrorLogs extends Command
{
    protected $signature = 'errors:clean {--days=30 : Días de retención}';
    protected $description = 'Eliminar errores de instance_error_logs con más de X días de antigüedad';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $deleted = InstanceErrorLog::where('created_at', '<', now()->subDays($days))->delete();

        $this->info("Se eliminaron {$deleted} registros de errores con más de {$days} días.");

        return Command::SUCCESS;
    }
}
