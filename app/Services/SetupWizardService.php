<?php

namespace App\Services;

use App\Models\BusinessType;
use App\Models\WizardStep;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SetupWizardService
{
    public function getSteps(User $user): array
    {
        // Los módulos visibles del rol del admin determinan qué pasos del wizard se muestran.
        // Si el admin tiene 'restaurante' → aparecen los pasos de mesas.
        // Si no tiene 'restaurante' → no aparecen, independientemente del BusinessType.
        $roleModules = $user->instanceRole?->visibleModules->pluck('modulo_key') ?? collect();

        // Si el admin no tiene rol con módulos configurados, usamos los módulos del BusinessType
        // para no dejar el wizard vacío en instancias recién creadas.
        if ($roleModules->isEmpty()) {
            $roleModules = collect(
                BusinessType::getModulosVisibles($user->businessInstance?->businessType?->slug)
            );
        }

        // Los módulos siempre requeridos, incluso si el admin no los tiene asignados
        $systemMods = collect(['ncf', 'configuracion-general']);

        $steps = WizardStep::where(function ($q) use ($roleModules, $systemMods) {
                $q->whereIn('module_key', $roleModules)
                  ->orWhereIn('module_key', $systemMods);
            })
            ->orderBy('orden')
            ->get()
            ->map(fn($ws) => [
                'key'        => $ws->key,
                'module_key' => $ws->module_key,
                'label'      => $ws->label,
                'icon'       => $ws->icon,
                'required'   => $ws->required,
                'skipable'   => $ws->skipable,
                'completed'  => $this->isStepCompleted($ws, $user),
            ])
            ->values()
            ->toArray();

        return $steps;
    }

    public function isStepCompleted(WizardStep $step, ?User $user = null): bool
    {
        if (!$step->entity_class || !class_exists($step->entity_class)) {
            return true;
        }

        $query = $step->entity_class::query();

        // Filtrar por tenant del usuario actual para evitar falsos positivos
        $tenantId = $user?->business_instance_id;
        if ($tenantId) {
            $table = (new $step->entity_class)->getTable();
            if (Schema::hasColumn($table, 'tenant_id')) {
                $query->where('tenant_id', $tenantId);
            }
        }

        return $query->count() > 0;
    }

    public function firstPendingStep(array $steps): ?array
    {
        foreach ($steps as $step) {
            if (!$step['completed']) {
                return $step;
            }
        }

        return null;
    }

    public function canComplete(array $steps): bool
    {
        foreach ($steps as $step) {
            if ($step['required'] && !$step['completed']) {
                return false;
            }
        }
        return true;
    }

    public function completedStepKeys(array $steps): array
    {
        return array_map(fn($s) => $s['key'], array_filter($steps, fn($s) => $s['completed']));
    }
}
