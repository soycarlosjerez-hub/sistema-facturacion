<?php

namespace App\Services;

use App\Models\BusinessType;
use App\Models\WizardStep;
use App\Models\User;

class SetupWizardService
{
    public function getSteps(User $user): array
    {
        $roleModules = $user->instanceRole?->visibleModules->pluck('modulo_key') ?? collect();
        $btModules = collect(
            BusinessType::getModulosVisibles($user->businessInstance?->businessType?->slug)
        );
        $systemMods = collect(['ncf']);

        $steps = WizardStep::whereIn('module_key', $roleModules)
            ->where(fn($q) => $q->whereIn('module_key', $btModules)
                ->orWhereIn('module_key', $systemMods))
            ->orderBy('orden')
            ->get()
            ->map(fn($ws) => [
                'key' => $ws->key,
                'module_key' => $ws->module_key,
                'label' => $ws->label,
                'icon' => $ws->icon,
                'required' => $ws->required,
                'skipable' => $ws->skipable,
                'completed' => $this->isStepCompleted($ws),
            ])
            ->values()
            ->toArray();

        return $steps;
    }

    public function isStepCompleted(WizardStep $step): bool
    {
        if (!$step->entity_class || !class_exists($step->entity_class)) {
            return true;
        }
        return $step->entity_class::count() > 0;
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
