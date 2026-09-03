<?php

namespace App\Policies;

use App\Models\MantenimientoEquipo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MantenimientoEquiposPolicy
{
    /**
     * Determine whether the user can view any equipment maintenance records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('mantenimiento-equipos.view');
    }

    /**
     * Determine whether the user can view a maintenance record.
     * Enforces tenant isolation.
     */
    public function view(User $user, MantenimientoEquipo $mantenimiento): bool
    {
        if ($mantenimiento->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('mantenimiento-equipos.view');
    }

    /**
     * Determine whether the user can create maintenance records.
     */
    public function create(User $user): bool
    {
        return $user->can('mantenimiento-equipos.create');
    }

    /**
     * Determine whether the user can update a maintenance record.
     * Enforces tenant isolation.
     */
    public function update(User $user, MantenimientoEquipo $mantenimiento): bool
    {
        if ($mantenimiento->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('mantenimiento-equipos.edit');
    }

    /**
     * Determine whether the user can delete a maintenance record.
     * Enforces tenant isolation.
     */
    public function delete(User $user, MantenimientoEquipo $mantenimiento): bool
    {
        if ($mantenimiento->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('mantenimiento-equipos.delete');
    }
}
