<?php

namespace App\Policies;

use App\Models\Tecnologia;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TecnologiaPolicy
{
    /**
     * Determine whether the user can view any tech products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('tecnologia.view');
    }

    /**
     * Determine whether the user can view a tech product.
     * Enforces tenant isolation.
     */
    public function view(User $user, Tecnologia $tecnologia): bool
    {
        if ($tecnologia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tecnologia.view');
    }

    /**
     * Determine whether the user can create tech products.
     */
    public function create(User $user): bool
    {
        return $user->can('tecnologia.create');
    }

    /**
     * Determine whether the user can update a tech product.
     * Enforces tenant isolation.
     */
    public function update(User $user, Tecnologia $tecnologia): bool
    {
        if ($tecnologia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tecnologia.edit');
    }

    /**
     * Determine whether the user can delete a tech product.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Tecnologia $tecnologia): bool
    {
        if ($tecnologia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('tecnologia.delete');
    }
}
