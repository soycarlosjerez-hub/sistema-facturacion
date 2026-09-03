<?php

namespace App\Policies;

use App\Models\Gasto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GastoPolicy
{
    /**
     * Determine whether the user can view any expenses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('gastos.view');
    }

    /**
     * Determine whether the user can view the expense.
     * Enforces tenant isolation.
     */
    public function view(User $user, Gasto $gasto): bool
    {
        if ($gasto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('gastos.view');
    }

    /**
     * Determine whether the user can create expenses.
     */
    public function create(User $user): bool
    {
        return $user->can('gastos.create');
    }

    /**
     * Determine whether the user can update the expense.
     * Enforces tenant isolation.
     */
    public function update(User $user, Gasto $gasto): bool
    {
        if ($gasto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('gastos.edit');
    }

    /**
     * Determine whether the user can delete the expense.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Gasto $gasto): bool
    {
        if ($gasto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('gastos.delete');
    }

    /**
     * Determine whether the user can restore a soft-deleted expense.
     */
    public function restore(User $user, Gasto $gasto): bool
    {
        if ($gasto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('gastos.delete');
    }

    /**
     * Determine whether the user can permanently delete an expense.
     */
    public function forceDelete(User $user, Gasto $gasto): bool
    {
        if ($gasto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('gastos.delete');
    }
}
