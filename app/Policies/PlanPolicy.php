<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlanPolicy
{
    /**
     * Determine whether the user can view any plans (owner only).
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('owner') || $user->can('owner.plans.view');
    }

    /**
     * Determine whether the user can view a plan.
     */
    public function view(User $user, Plan $plan): bool
    {
        return $user->hasRole('owner') || $user->can('owner.plans.view');
    }

    /**
     * Determine whether the user can create plans (owner only).
     */
    public function create(User $user): bool
    {
        return $user->hasRole('owner') || $user->can('owner.plans.create');
    }

    /**
     * Determine whether the user can update a plan (owner only).
     */
    public function update(User $user, Plan $plan): bool
    {
        return $user->hasRole('owner') || $user->can('owner.plans.edit');
    }

    /**
     * Determine whether the user can delete a plan (owner only).
     */
    public function delete(User $user, Plan $plan): bool
    {
        return $user->hasRole('owner') || $user->can('owner.plans.delete');
    }

    /**
     * Determine whether the user can change their own instance plan.
     */
    public function cambiarPlan(User $user): bool
    {
        return true; // Each user can request plan changes for their instance
    }

    /**
     * Determine whether the user can view plan limits for current instance.
     */
    public function verLimites(User $user): bool
    {
        return true;
    }
}
