<?php

namespace App\Policies;

use App\Models\Tattoo;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TattooPolicy
{
    /**
     * Determine whether the user can view any tattoos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('tattoo.view');
    }

    /**
     * Determine whether the user can view a tattoo.
     * Enforces tenant isolation.
     */
    public function view(User $user, Tattoo $tattoo): bool
    {
        if ($tattoo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tattoo.view');
    }

    /**
     * Determine whether the user can create tattoos.
     */
    public function create(User $user): bool
    {
        return $user->can('tattoo.create');
    }

    /**
     * Determine whether the user can update a tattoo.
     * Enforces tenant isolation.
     */
    public function update(User $user, Tattoo $tattoo): bool
    {
        if ($tattoo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tattoo.edit');
    }

    /**
     * Determine whether the user can delete a tattoo.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Tattoo $tattoo): bool
    {
        if ($tattoo->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tattoo.delete');
    }

    /**
     * Determine whether the user can manage appointments.
     */
    public function gestionarCitas(User $user): bool
    {
        return $user->can('tattoo.citas.view');
    }
}
