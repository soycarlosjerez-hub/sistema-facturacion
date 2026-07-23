<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Mantenimiento;
use Illuminate\Auth\Access\Response;

class MantenimientoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::mantenimiento');
    }

    public function view(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can('view_climatizacion::mantenimiento');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::mantenimiento');
    }

    public function update(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can('update_climatizacion::mantenimiento');
    }

    public function delete(User $user, Mantenimiento $mantenimiento): bool
    {
        return $user->can('delete_climatizacion::mantenimiento');
    }
}
