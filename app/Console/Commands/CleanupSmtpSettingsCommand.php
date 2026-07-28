<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;

class CleanupSmtpSettingsCommand extends Command
{
    protected $signature = 'smtp:cleanup {--dry-run}';
    protected $description = 'Elimina los settings SMTP individuales de todas las instancias. El SMTP será global.';

    public function handle(): int
    {
        $mailKeys = ['mail_mailer', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_from_address', 'mail_from_name'];

        $affectedRows = SystemSetting::whereIn('key', $mailKeys)
            ->whereNotNull('tenant_id')
            ->count();

        if ($affectedRows === 0) {
            $this->info('No hay settings SMTP de instancias para limpiar.');
            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('=== DRY RUN ===');
            $this->line("Se eliminarían {$affectedRows} setting(s) SMTP de instancias:\n");
            
            $settings = SystemSetting::whereIn('key', $mailKeys)
                ->whereNotNull('tenant_id')
                ->select('key', 'tenant_id')
                ->get();
            
            $byInstance = $settings->groupBy('tenant_id');
            foreach ($byInstance as $tenantId => $keys) {
                $this->line("  Instancia {$tenantId}: " . implode(', ', $keys->pluck('key')->toArray()));
            }
            $this->line("\nEjecuta sin --dry-run para aplicar los cambios.");
            return Command::SUCCESS;
        }

        $deleted = SystemSetting::whereIn('key', $mailKeys)
            ->whereNotNull('tenant_id')
            ->delete();

        SystemSetting::flush();

        $this->info("✓ Eliminados {$deleted} settings SMTP de instancias.");
        $this->info('Las instancias ahora usarán el SMTP global configurado en el panel de Owner.');

        return Command::SUCCESS;
    }
}
