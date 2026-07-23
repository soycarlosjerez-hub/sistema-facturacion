<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TipoClima;
use Illuminate\Auth\Access\Response;

class TipoClimaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::tipo_clima');
    }

    public function view(User $user, TipoClima $tipoClima): bool
    {
        return $user->can('view_climatizacion::tipo_clima');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::tipo_clima');
    }

    public function update(User $user, TipoClima $tipoClima): bool
    {
        return $user->can('update_climatizacion::tipo_clima');
    }

    public function delete(User $user, TipoClima $tipoClima): bool
    {
        return $user->can('delete_climatizacion::tipo_clima');
    }
}
