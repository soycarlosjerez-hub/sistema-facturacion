<?php

namespace App\Policies;

use App\Models\Ncf;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NcfPolicy
{
    /**
     * Determine whether the user can view any NCFs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ncf.view');
    }

    /**
     * Determine whether the user can view the NCF.
     * Enforces tenant isolation.
     */
    public function view(User $user, Ncf $ncf): bool
    {
        if ($ncf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ncf.view');
    }

    /**
     * Determine whether the user can create NCFs.
     */
    public function create(User $user): bool
    {
        return $user->can('ncf.manage');
    }

    /**
     * Determine whether the user can update the NCF.
     * Enforces tenant isolation.
     */
    public function update(User $user, Ncf $ncf): bool
    {
        if ($ncf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ncf.manage');
    }

    /**
     * Determine whether the user can delete the NCF.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Ncf $ncf): bool
    {
        if ($ncf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ncf.manage');
    }

    /**
     * Determine whether the user can validate an NCF with DGII.
     */
    public function validar(User $user, Ncf $ncf): bool
    {
        if ($ncf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ncf.manage');
    }
}
