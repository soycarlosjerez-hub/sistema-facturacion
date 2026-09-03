<?php

namespace App\Policies;

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EquiposPolicy
{
    /**
     * Determine whether the user can view any equipment.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('equipos.view');
    }

    /**
     * Determine whether the user can view an equipment.
     * Enforces tenant isolation.
     */
    public function view(User $user, Equipo $equipo): bool
    {
        if ($equipo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('equipos.view');
    }

    /**
     * Determine whether the user can create equipment.
     */
    public function create(User $user): bool
    {
        return $user->can('equipos.create');
    }

    /**
     * Determine whether the user can update an equipment.
     * Enforces tenant isolation.
     */
    public function update(User $user, Equipo $equipo): bool
    {
        if ($equipo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('equipos.edit');
    }

    /**
     * Determine whether the user can delete an equipment.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Equipo $equipo): bool
    {
        if ($equipo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('equipos.delete');
    }

    /**
     * Determine whether the user can manage equipment state (availability).
     */
    public function gestionarEstado(User $user, Equipo $equipo): bool
    {
        if ($equipo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('equipos.edit');
    }
}
