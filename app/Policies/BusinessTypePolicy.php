<?php

namespace App\Policies;

use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('business-types.view') || $user->can('configuracion.view');
    }

    public function view(User $user, BusinessType $businessType): bool
    {
        return $user->can('business-types.view') || $user->can('configuracion.view');
    }

    public function create(User $user): bool
    {
        return $user->can('business-types.create') || $user->can('configuracion.edit');
    }

    public function update(User $user, BusinessType $businessType): bool
    {
        return $user->can('business-types.edit') || $user->can('configuracion.edit');
    }

    public function delete(User $user, BusinessType $businessType): bool
    {
        return $user->can('business-types.delete') || $user->can('configuracion.edit');
    }

    public function manage(User $user): bool
    {
        return $user->can('business-types.edit') || $user->can('configuracion.edit');
    }
}
