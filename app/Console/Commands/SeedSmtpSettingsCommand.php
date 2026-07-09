<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class SeedSmtpSettingsCommand extends Command
{
    protected $signature = 'smtp:seed {--dry-run}';
    protected $description = 'Aplicar configuración SMTP por defecto a todas las instancias';

    public function handle(): int
    {
        $instances = BusinessInstance::where('activo', true)->get();

        if ($instances->isEmpty()) {
            $this->info('No hay instancias activas.');
            return Command::SUCCESS;
        }

        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('=== DRY RUN ===');
            $this->line("Se aplicarían SMTP settings a {$instances->count()} instancia(s):\n");
            foreach ($instances as $instance) {
                $this->line("  - {$instance->nombre} (ID: {$instance->id})");
            }
            $this->line("\nEjecuta sin --dry-run para aplicar los cambios.");
            return Command::SUCCESS;
        }

        $this->info('Aplicando SMTP settings a instancias activas...');

        $count = 0;
        foreach ($instances as $instance) {
            $settings = [
                'mail_mailer'     => 'smtp',
                'mail_host'       => 'mail.armada.do',
                'mail_port'       => '465',
                'mail_username'   => 'no-reply@armada.do',
                'mail_password'   => Crypt::encryptString('Dn%q#U0tV,65FqSU'),
                'mail_encryption' => 'ssl',
                'mail_from_address' => 'no-reply@armada.do',
                'mail_from_name'    => 'Sistema de Facturación',
            ];

            foreach ($settings as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key, 'tenant_id' => $instance->id],
                    ['value' => $value]
                );
            }

            SystemSetting::flush();
            $this->line("  ✓ {$instance->nombre} (ID: {$instance->id})");
            $count++;
        }

        $this->info("SMTP settings aplicados a {$count} instancia(s).");
        return Command::SUCCESS;
    }
}
