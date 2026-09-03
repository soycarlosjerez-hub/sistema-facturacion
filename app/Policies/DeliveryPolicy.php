<?php

namespace App\Policies;

use App\Models\DeliveryCompany;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view any delivery companies.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('delivery-companies.view');
    }

    /**
     * Determine whether the user can view a delivery company.
     * Enforces tenant isolation.
     */
    public function view(User $user, DeliveryCompany $delivery): bool
    {
        if ($delivery->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('delivery-companies.view');
    }

    /**
     * Determine whether the user can create delivery companies.
     */
    public function create(User $user): bool
    {
        return $user->can('delivery-companies.create');
    }

    /**
     * Determine whether the user can update a delivery company.
     * Enforces tenant isolation.
     */
    public function update(User $user, DeliveryCompany $delivery): bool
    {
        if ($delivery->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('delivery-companies.edit');
    }

    /**
     * Determine whether the user can delete a delivery company.
     * Enforces tenant isolation.
     */
    public function delete(User $user, DeliveryCompany $delivery): bool
    {
        if ($delivery->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('delivery-companies.delete');
    }
}
