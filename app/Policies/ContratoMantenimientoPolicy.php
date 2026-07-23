<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContratoMantenimiento;
use Illuminate\Auth\Access\Response;

class ContratoMantenimientoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::contrato_mantenimiento');
    }

    public function view(User $user, ContratoMantenimiento $contrato): bool
    {
        return $user->can('view_climatizacion::contrato_mantenimiento');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::contrato_mantenimiento');
    }

    public function update(User $user, ContratoMantenimiento $contrato): bool
    {
        return $user->can('update_climatizacion::contrato_mantenimiento');
    }

    public function delete(User $user, ContratoMantenimiento $contrato): bool
    {
        return $user->can('delete_climatizacion::contrato_mantenimiento');
    }
}
