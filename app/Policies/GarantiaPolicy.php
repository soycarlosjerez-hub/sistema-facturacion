<?php

namespace App\Policies;

use App\Models\Garantia;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GarantiaPolicy
{
    /**
     * Determine whether the user can view any warranties.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('garantias.view');
    }

    /**
     * Determine whether the user can view a warranty.
     * Enforces tenant isolation.
     */
    public function view(User $user, Garantia $garantia): bool
    {
        if ($garantia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('garantias.view');
    }

    /**
     * Determine whether the user can create warranties.
     */
    public function create(User $user): bool
    {
        return $user->can('garantias.create');
    }

    /**
     * Determine whether the user can update a warranty.
     * Enforces tenant isolation.
     */
    public function update(User $user, Garantia $garantia): bool
    {
        if ($garantia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('garantias.edit');
    }

    /**
     * Determine whether the user can delete a warranty.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Garantia $garantia): bool
    {
        if ($garantia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('garantias.delete');
    }

    /**
     * Determine whether the user can activate a warranty claim.
     */
    public function activarReclamo(User $user, Garantia $garantia): bool
    {
        if ($garantia->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('garantias.edit');
    }
}
