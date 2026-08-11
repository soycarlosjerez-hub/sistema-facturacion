<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionSuspendedMail;
use App\Models\BusinessInstance;
use App\Models\InstanceErrorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BillingVerify extends Command
{
    protected $signature = 'billing:verificar {--dry-run : Muestra qué instancias serían bloqueadas sin ejecutar el bloqueo}';
    protected $description = 'Verifica las suscripciones y bloquea automáticamente las instancias con pagos vencidos';

    public function handle(): int
    {
        $instances = BusinessInstance::query()
            ->with('plan')
            ->where('activo', true)
            ->where('bloqueado', false)
            ->get();

        $blocked = 0;
        $skipped = 0;
        $dryRun = (bool) $this->option('dry-run');

        foreach ($instances as $instance) {
            try {
                if (! $instance->bloqueablePorImpago()) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY-RUN] Se bloquearía: {$instance->nombre} (deuda: RD\$" . number_format($instance->deudaEstimada(), 2) . ')');
                    $skipped++;
                    continue;
                }

                $instance->update([
                    'bloqueado' => true,
                    'motivo_bloqueo' => 'Impago de suscripción',
                    'bloqueado_en' => now(),
                ]);

                \App\Models\AuditLog::create([
                    'user_id' => null,
                    'action' => 'INSTANCE_AUTO_BLOCK',
                    'model_type' => BusinessInstance::class,
                    'model_id' => $instance->id,
                    'description' => "Instancia '{$instance->nombre}' bloqueada automáticamente por impago de suscripción.",
                    'tenant_id' => null,
                ]);

                $this->sendSuspensionNotification($instance);
                $blocked++;
            } catch (\Throwable $e) {
                Log::error('billing:verificar — error procesando instancia ' . $instance->id . ': ' . $e->getMessage());
                InstanceErrorLog::create([
                    'tenant_id' => $instance->id,
                    'level' => 'error',
                    'title' => 'billing:verificar — ' . $e->getMessage(),
                    'message' => $e->getMessage() . "\n" . $e->getTraceAsString(),
                    'source' => 'billing',
                ]);
            }
        }

        $this->info("Proceso completado. Instancias bloqueadas: {$blocked}." . ($dryRun ? " (DRY-RUN, no se ejecutó ningún cambio)" : ''));

        return Command::SUCCESS;
    }

    private function sendSuspensionNotification(BusinessInstance $instance): void
    {
        $recipients = collect([$instance->owner_email])
            ->merge($instance->users()->pluck('email'))
            ->filter()
            ->unique()
            ->take(3);

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new SubscriptionSuspendedMail($instance));
            } catch (\Throwable $e) {
                Log::warning("billing:verificar — no se pudo notificar a {$email}: " . $e->getMessage());
            }
        }
    }
}
