<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\InstanceErrorLog;
use App\Services\BillingNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BillingVerify extends Command
{
    protected $signature = 'billing:verificar {--dry-run : Muestra qué instancias serían bloqueadas sin ejecutar el bloqueo}';
    protected $description = 'Verifica las suscripciones, envía recordatorios y bloquea automáticamente las instancias con pagos vencidos';

    public function __construct(protected BillingNotificationService $billing)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $instances = BusinessInstance::query()
            ->with('plan')
            ->where('activo', true)
            ->where('bloqueado', false)
            ->get();

        $blocked = 0;
        $reminders = 0;
        $dryRun = (bool) $this->option('dry-run');

        foreach ($instances as $instance) {
            try {
                $reminders += $this->sendReminders($instance);

                if (! $instance->bloqueablePorImpago()) {
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY-RUN] Se bloquearía: {$instance->nombre} (deuda: RD\$" . number_format($instance->deudaEstimada(), 2) . ')');
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

                $this->billing->suspension($instance);
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

        $this->info("Proceso completado. Recordatorios enviados: {$reminders}. Instancias bloqueadas: {$blocked}." . ($dryRun ? " (DRY-RUN, no se ejecutó ningún cambio)" : ''));

        return Command::SUCCESS;
    }

    private function sendReminders(BusinessInstance $instance): int
    {
        $sent = 0;

        if ($instance->enPeriodoPrueba()) {
            $dias = $instance->diasPruebaRestantes();

            if ($dias === 3 && $this->marcar($instance, 'billing_trial_3')) {
                $this->billing->recordatorioPrueba($instance);
                $this->line("  [OK] Recordatorio de prueba (3 días) → {$instance->nombre}");
                $sent++;
            }

            if ($dias === 1 && $this->marcar($instance, 'billing_trial_1')) {
                $this->billing->recordatorioPrueba($instance);
                $this->line("  [OK] Recordatorio de prueba (1 día) → {$instance->nombre}");
                $sent++;
            }

            return $sent;
        }

        if ($instance->estaAlDia() && $instance->ultimoPagoConfirmado()->exists()) {
            $proximo = $instance->proximoPagoEsperado();
            if ($proximo) {
                $diasParaVencer = max(0, (int) now()->startOfDay()->diffInDays($proximo->copy()->startOfDay()));
                $marker = 'billing_renewal_' . $proximo->format('Y-m');

                if ($diasParaVencer === 3 && $this->marcar($instance, $marker)) {
                    $this->billing->recordatorioRenovacion($instance);
                    $this->line("  [OK] Recordatorio de renovación → {$instance->nombre}");
                    $sent++;
                }
            }
        }

        return $sent;
    }

    /**
     * Marca un hito de facturación en la configuración JSON de la instancia.
     * Devuelve true si la marca es nueva (permite enviar la notificación una sola vez).
     */
    private function marcar(BusinessInstance $instance, string $key): bool
    {
        $cfg = $instance->configuracion ?? [];
        if (array_key_exists($key, $cfg)) {
            return false;
        }

        $cfg[$key] = now()->toDateString();
        $instance->update(['configuracion' => $cfg]);

        return true;
    }
}