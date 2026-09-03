<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Spatie Role model policy.
 */
class RolePolicy
{
    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine whether the user can view a role.
     */
    public function view(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine whether the user can update a role.
     */
    public function update(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $user->can('roles.edit');
    }

    /**
     * Determine whether the user can delete a role.
     * Protected system roles (admin, owner, etc.) cannot be deleted.
     */
    public function delete(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        // Prevent deletion of critical system roles
        $protectedRoles = ['admin', 'owner', 'admin-business', 'root', 'gerente'];
        if (in_array($role->name, protectedRoles)) {
            return false;
        }
        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can assign permissions to a role.
     */
    public function asignarPermisos(User $user, \Spatie\Permission\Models\Role $role): bool
    {
        return $user->can('roles.edit');
    }
}
