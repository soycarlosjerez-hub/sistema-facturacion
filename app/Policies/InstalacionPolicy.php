<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Instalacion;
use Illuminate\Auth\Access\Response;

class InstalacionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::instalacion');
    }

    public function view(User $user, Instalacion $instalacion): bool
    {
        return $user->can('view_climatizacion::instalacion');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::instalacion');
    }

    public function update(User $user, Instalacion $instalacion): bool
    {
        return $user->can('update_climatizacion::instalacion');
    }

    public function delete(User $user, Instalacion $instalacion): bool
    {
        return $user->can('delete_climatizacion::instalacion');
    }
}
