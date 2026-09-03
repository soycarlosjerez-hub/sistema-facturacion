<?php

namespace App\Policies;

use App\Models\Conduce;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ConducePolicy
{
    /**
     * Determine whether the user can view any conduces (delivery manifests).
     */
    public function viewAny(User $user): bool
    {
        return $user->can('conduces.view');
    }

    /**
     * Determine whether the user can view the conduces.
     * Enforces tenant isolation.
     */
    public function view(User $user, Conduce $conduce): bool
    {
        if ($conduce->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('conduces.view');
    }

    /**
     * Determine whether the user can create conduces.
     */
    public function create(User $user): bool
    {
        return $user->can('conduces.create');
    }

    /**
     * Determine whether the user can update the conduces.
     * Enforces tenant isolation.
     */
    public function update(User $user, Conduce $conduce): bool
    {
        if ($conduce->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('conduces.edit');
    }

    /**
     * Determine whether the user can delete the conduces.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Conduce $conduce): bool
    {
        if ($conduce->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('conduces.delete');
    }

    /**
     * Determine whether the user can export conduces as PDF.
     */
    public function exportarPdf(User $user, Conduce $conduce): bool
    {
        if ($conduce->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('conduces.view');
    }
}
