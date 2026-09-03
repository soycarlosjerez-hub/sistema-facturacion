<?php

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProveedorPolicy
{
    /**
     * Determine whether the user can view any suppliers.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('proveedores.view');
    }

    /**
     * Determine whether the user can view the supplier.
     * Enforces tenant isolation.
     */
    public function view(User $user, Proveedor $proveedor): bool
    {
        if ($proveedor->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('proveedores.view');
    }

    /**
     * Determine whether the user can create suppliers.
     */
    public function create(User $user): bool
    {
        return $user->can('proveedores.create');
    }

    /**
     * Determine whether the user can update the supplier.
     * Enforces tenant isolation.
     */
    public function update(User $user, Proveedor $proveedor): bool
    {
        if ($proveedor->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('proveedores.edit');
    }

    /**
     * Determine whether the user can delete the supplier.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Proveedor $proveedor): bool
    {
        if ($proveedor->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('proveedores.delete');
    }

    /**
     * Determine whether the user can restore a soft-deleted supplier.
     */
    public function restore(User $user, Proveedor $proveedor): bool
    {
        if ($proveedor->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('proveedores.delete');
    }

    /**
     * Determine whether the user can permanently delete a supplier.
     */
    public function forceDelete(User $user, Proveedor $proveedor): bool
    {
        if ($proveedor->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('proveedores.delete');
    }
}
