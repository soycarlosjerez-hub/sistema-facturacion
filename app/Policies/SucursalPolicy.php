<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SucursalPolicy
{
    /**
     * Determine whether the user can view any branches.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sucursales.view');
    }

    /**
     * Determine whether the user can view the branch.
     * Enforces tenant isolation.
     */
    public function view(User $user, Sucursal $sucursal): bool
    {
        if ($sucursal->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('sucursales.view');
    }

    /**
     * Determine whether the user can create branches.
     */
    public function create(User $user): bool
    {
        return $user->can('sucursales.create');
    }

    /**
     * Determine whether the user can update the branch.
     * Enforces tenant isolation.
     */
    public function update(User $user, Sucursal $sucursal): bool
    {
        if ($sucursal->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('sucursales.edit');
    }

    /**
     * Determine whether the user can delete the branch.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Sucursal $sucursal): bool
    {
        if ($sucursal->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('sucursales.delete');
    }

    /**
     * Determine whether the user can manage branch settings.
     */
    public function manage(User $user, Sucursal $sucursal): bool
    {
        if ($sucursal->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('sucursales.edit');
    }
}
