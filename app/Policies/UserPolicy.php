<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     * Admins/owners only.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('usuarios.view');
    }

    /**
     * Determine whether the user can view another user.
     * Enforces tenant isolation.
     */
    public function view(User $user, User $model): bool
    {
        if ($model->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('usuarios.view');
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('usuarios.create');
    }

    /**
     * Determine whether the user can update another user.
     * Enforces tenant isolation.
     */
    public function update(User $user, User $model): bool
    {
        if ($model->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('usuarios.edit');
    }

    /**
     * Determine whether the user can delete a user.
     * Enforces tenant isolation.
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('usuarios.delete');
    }

    /**
     * Determine whether the user can assign roles to a user.
     */
    public function asignarRol(User $user, User $model): bool
    {
        if ($model->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('usuarios.edit');
    }

    /**
     * Determine whether the user can manage the owner's own profile.
     */
    public function verPerfil(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can edit their own profile.
     */
    public function editarPerfil(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can change their own password.
     */
    public function cambiarPassword(User $user): bool
    {
        return true;
    }
}
