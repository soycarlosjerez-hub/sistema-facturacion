<?php

namespace App\Policies;

use App\Models\Orden;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrdenPolicy
{
    /**
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ordenes.view');
    }

    /**
     * Determine whether the user can view the order.
     * Enforces tenant isolation.
     */
    public function view(User $user, Orden $orden): bool
    {
        if ($orden->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ordenes.view');
    }

    /**
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->can('ordenes.create');
    }

    /**
     * Determine whether the user can update the order.
     * Enforces tenant isolation.
     */
    public function update(User $user, Orden $orden): bool
    {
        if ($orden->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ordenes.edit');
    }

    /**
     * Determine whether the user can delete the order.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Orden $orden): bool
    {
        if ($orden->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ordenes.delete');
    }

    /**
     * Determine whether the user can update order status.
     */
    public function cambiarEstado(User $user, Orden $orden): bool
    {
        if ($orden->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ordenes.edit');
    }
}
