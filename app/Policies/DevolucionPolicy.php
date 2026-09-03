<?php

namespace App\Policies;

use App\Models\Devolucion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DevolucionPolicy
{
    /**
     * Determine whether the user can view any returns.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('devoluciones.view');
    }

    /**
     * Determine whether the user can view the return.
     * Enforces tenant isolation.
     */
    public function view(User $user, Devolucion $devolucion): bool
    {
        if ($devolucion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('devoluciones.view');
    }

    /**
     * Determine whether the user can create returns.
     */
    public function create(User $user): bool
    {
        return $user->can('devoluciones.create');
    }

    /**
     * Determine whether the user can update the return.
     * Enforces tenant isolation.
     */
    public function update(User $user, Devolucion $devolucion): bool
    {
        if ($devolucion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('devoluciones.edit');
    }

    /**
     * Determine whether the user can delete the return.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Devolucion $devolucion): bool
    {
        if ($devolucion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('devoluciones.delete');
    }

    /**
     * Determine whether the user can authorize a return (higher privilege).
     */
    public function autorizar(User $user, Devolucion $devolucion): bool
    {
        if ($devolucion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('devoluciones.edit');
    }
}
