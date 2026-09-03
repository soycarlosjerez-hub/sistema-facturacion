<?php

namespace App\Policies;

use App\Models\Presupuesto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PresupuestoPolicy
{
    /**
     * Determine whether the user can view any budgets.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('presupuestos.view');
    }

    /**
     * Determine whether the user can view a budget.
     * Enforces tenant isolation.
     */
    public function view(User $user, Presupuesto $presupuesto): bool
    {
        if ($presupuesto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('presupuestos.view');
    }

    /**
     * Determine whether the user can create budgets.
     */
    public function create(User $user): bool
    {
        return $user->can('presupuestos.create');
    }

    /**
     * Determine whether the user can update a budget.
     * Enforces tenant isolation.
     */
    public function update(User $user, Presupuesto $presupuesto): bool
    {
        if ($presupuesto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('presupuestos.edit');
    }

    /**
     * Determine whether the user can delete a budget.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Presupuesto $presupuesto): bool
    {
        if ($presupuesto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('presupuestos.delete');
    }

    /**
     * Determine whether the user can approve a budget.
     */
    public function aprobar(User $user, Presupuesto $presupuesto): bool
    {
        if ($presupuesto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('presupuestos.approbar');
    }

    /**
     * Determine whether the user can convert budget to sale.
     */
    public function convertirVenta(User $user, Presupuesto $presupuesto): bool
    {
        if ($presupuesto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('presupuestos.edit');
    }
}
