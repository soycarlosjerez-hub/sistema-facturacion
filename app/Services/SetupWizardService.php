<?php

namespace App\Services;

use App\Models\BusinessType;
use App\Models\WizardStep;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SetupWizardService
{
    protected array $businessTypeModules = [
        'general' => [
            'ncf', 'configuracion-general', 'sucursales', 'almacenes',
            'cajas', 'inventario', 'proveedores', 'clientes',
        ],
        'restaurante' => [
            'restaurante',
        ],
        'lavadero' => [
            'lavadero',
        ],
        'climatizacion' => [
            'climatizacion', 'climatizacion-tipos-equipos',
            'climatizacion-instalaciones', 'climatizacion-contratos',
            'climatizacion-mantenimientos',
        ],
        'tecnologia' => [
            'equipos', 'tecnicas', 'tecnicos', 'domotica', 'garantias',
        ],
    ];

    public function getSteps(User $user): array
    {
        $instance = $user->businessInstance;
        $businessTypeSlug = $instance?->businessType?->slug;

        $roleModules = $user->instanceRole?->visibleModules->pluck('modulo_key') ?? collect();

        if ($roleModules->isEmpty()) {
            $roleModules = collect(
                BusinessType::getModulosVisibles($businessTypeSlug)
            );
        }

        $systemMods = collect(['ncf', 'configuracion-general']);

        $allowedModuleKeys = $this->getAllowedModuleKeys($businessTypeSlug);

        $steps = WizardStep::where(function ($q) use ($roleModules, $systemMods, $allowedModuleKeys) {
                $q->whereIn('module_key', $roleModules)
                  ->orWhereIn('module_key', $systemMods);
            })
            ->whereIn('module_key', $allowedModuleKeys)
            ->orWhere(function ($q) use ($systemMods) {
                $q->whereIn('module_key', $systemMods);
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

    protected function getAllowedModuleKeys(?string $businessTypeSlug): array
    {
        if (!$businessTypeSlug) {
            return $this->businessTypeModules['general'];
        }

        $keys = $this->businessTypeModules['general'];

        foreach ($this->businessTypeModules as $type => $mods) {
            if ($type !== 'general' && strpos($businessTypeSlug, $type) !== false) {
                $keys = array_merge($keys, $mods);
            }
        }

        if (in_array('restaurante', explode('-', $businessTypeSlug))) {
            $keys = array_unique(array_merge($keys, $this->businessTypeModules['restaurante']));
        }
        if (in_array('lavadero', explode('-', $businessTypeSlug))) {
            $keys = array_unique(array_merge($keys, $this->businessTypeModules['lavadero']));
        }

        return array_unique($keys);
    }

    public function isStepCompleted(WizardStep $step, ?User $user = null): bool
    {
        if (!$step->entity_class || !class_exists($step->entity_class)) {
            return true;
        }

        $query = $step->entity_class::query();

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
