<?php

namespace App\Policies;

use App\Models\Arte;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArtePolicy
{
    /**
     * Determine whether the user can view any art pieces.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('arte.view');
    }

    /**
     * Determine whether the user can view an art piece.
     * Enforces tenant isolation.
     */
    public function view(User $user, Arte $arte): bool
    {
        if ($arte->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('arte.view');
    }

    /**
     * Determine whether the user can create art pieces.
     */
    public function create(User $user): bool
    {
        return $user->can('arte.create');
    }

    /**
     * Determine whether the user can update an art piece.
     * Enforces tenant isolation.
     */
    public function update(User $user, Arte $arte): bool
    {
        if ($arte->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('arte.edit');
    }

    /**
     * Determine whether the user can delete an art piece.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Arte $arte): bool
    {
        if ($arte->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('arte.delete');
    }
}
