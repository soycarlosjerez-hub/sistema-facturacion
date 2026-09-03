<?php

namespace App\Policies;

use App\Models\Caja;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CajaPolicy
{
    /**
     * Determine whether the user can view any cash registers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('cajas.view');
    }

    /**
     * Determine whether the user can view the cash register.
     * Enforces tenant isolation.
     */
    public function view(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.view');
    }

    /**
     * Determine whether the user can create cash registers.
     */
    public function create(User $user): bool
    {
        return $user->can('cajas.create');
    }

    /**
     * Determine whether the user can update the cash register.
     * Enforces tenant isolation.
     */
    public function update(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.edit');
    }

    /**
     * Determine whether the user can delete the cash register.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.delete');
    }

    /**
     * Determine whether the user can open a cash register session.
     */
    public function abrir(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.create');
    }

    /**
     * Determine whether the user can close a cash register session.
     * Regular users can only close their own sessions.
     * Admins/owners can close any session.
     */
    public function cerrar(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.edit');
    }

    /**
     * Determine whether the user can view a cash register session.
     * Regular users can only view their own sessions unless they are admin/owner.
     */
    public function verSesion(User $user, Caja $caja): bool
    {
        if ($caja->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cajas.view');
    }
}
