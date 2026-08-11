<?php

namespace App\Console\Commands;

use App\Models\BusinessInstance;
use App\Models\Plan;
use Illuminate\Console\Command;

class PlansBackfill extends Command
{
    protected $signature = 'plans:backfill {--plan=profesional : Slug del plan por defecto para instancias sin plan} {--dry-run : Muestra las instancias a actualizar sin ejecutar cambios}';

    protected $description = 'Asigna un plan a las instancias existentes que no tienen plan asignado';

    public function handle(): int
    {
        $slug = (string) $this->option('plan');
        $dryRun = (bool) $this->option('dry-run');

        $plan = Plan::where('slug', $slug)->first();

        if (! $plan) {
            $this->error("No existe un plan con slug '{$slug}'.");

            return Command::FAILURE;
        }

        $instances = BusinessInstance::withTrashed()
            ->whereNull('plan_id')
            ->get();

        if ($instances->isEmpty()) {
            $this->info('Todas las instancias ya tienen plan asignado.');

            return Command::SUCCESS;
        }

        foreach ($instances as $instance) {
            $costo = $instance->costo_mensual ?? $plan->precio_mensual;

            if ($dryRun) {
                $this->line("  [DRY-RUN] Se asignaría plan '{$plan->slug}' a '{$instance->nombre}' (costo_mensual: {$costo})");
                continue;
            }

            $instance->update([
                'plan_id' => $plan->id,
                'costo_mensual' => $costo,
            ]);
        }

        $this->info('Backfill de planes completado: ' . count($instances) . ' instancia(s) procesada(s).');

        return Command::SUCCESS;
    }
}
