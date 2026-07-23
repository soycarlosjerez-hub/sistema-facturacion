<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrdenEmergencia;
use Illuminate\Auth\Access\Response;

class OrdenEmergenciaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::orden_emergencia');
    }

    public function view(User $user, OrdenEmergencia $orden): bool
    {
        return $user->can('view_climatizacion::orden_emergencia');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::orden_emergencia');
    }

    public function update(User $user, OrdenEmergencia $orden): bool
    {
        return $user->can('update_climatizacion::orden_emergencia');
    }

    public function delete(User $user, OrdenEmergencia $orden): bool
    {
        return $user->can('delete_climatizacion::orden_emergencia');
    }
}
