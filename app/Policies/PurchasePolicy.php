<?php

namespace App\Policies;

use App\Models\Compra;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PurchasePolicy
{
    /**
     * Determine whether the user can view any purchases.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('compras.view');
    }

    /**
     * Determine whether the user can view the purchase.
     * Enforces tenant isolation.
     */
    public function view(User $user, Compra $compra): bool
    {
        if ($compra->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('compras.view');
    }

    /**
     * Determine whether the user can create purchases.
     */
    public function create(User $user): bool
    {
        return $user->can('compras.create');
    }

    /**
     * Determine whether the user can update the purchase.
     * Enforces tenant isolation.
     */
    public function update(User $user, Compra $compra): bool
    {
        if ($compra->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('compras.edit');
    }

    /**
     * Determine whether the user can delete the purchase.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Compra $compra): bool
    {
        if ($compra->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('compras.delete');
    }

    /**
     * Determine whether the user can delete a purchase detail.
     */
    public function deleteDetail(User $user, Compra $compra): bool
    {
        if ($compra->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('compras.edit');
    }

    /**
     * Determine whether the user can generate e-CF E41 for a purchase.
     */
    public function generarEcf(User $user, Compra $compra): bool
    {
        if ($compra->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('compras.edit');
    }
}
