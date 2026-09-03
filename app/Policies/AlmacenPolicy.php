<?php

namespace App\Policies;

use App\Models\Almacen;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlmacenPolicy
{
    /**
     * Determine whether the user can view any warehouses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('almacenes.view');
    }

    /**
     * Determine whether the user can view the warehouse.
     * Enforces tenant isolation.
     */
    public function view(User $user, Almacen $almacen): bool
    {
        if ($almacen->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('almacenes.view');
    }

    /**
     * Determine whether the user can create warehouses.
     */
    public function create(User $user): bool
    {
        return $user->can('almacenes.create');
    }

    /**
     * Determine whether the user can update the warehouse.
     * Enforces tenant isolation.
     */
    public function update(User $user, Almacen $almacen): bool
    {
        if ($almacen->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('almacenes.edit');
    }

    /**
     * Determine whether the user can delete the warehouse.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Almacen $almacen): bool
    {
        if ($almacen->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('almacenes.delete');
    }

    /**
     * Determine whether the user can view inventory movements (kardex).
     */
    public function verKardex(User $user, Almacen $almacen): bool
    {
        if ($almacen->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('kardex.view');
    }
}
