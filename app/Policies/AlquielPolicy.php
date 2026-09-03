<?php

namespace App\Policies;

use App\Models\Alquiler;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AlquielPolicy
{
    /**
     * Determine whether the user can view any rentals.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('alquileres.view');
    }

    /**
     * Determine whether the user can view a rental.
     * Enforces tenant isolation.
     */
    public function view(User $user, Alquiler $alquiler): bool
    {
        if ($alquiler->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('alquileres.view');
    }

    /**
     * Determine whether the user can create rentals.
     */
    public function create(User $user): bool
    {
        return $user->can('alquileres.create');
    }

    /**
     * Determine whether the user can update a rental.
     * Enforces tenant isolation.
     */
    public function update(User $user, Alquiler $alquiler): bool
    {
        if ($alquiler->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('alquileres.edit');
    }

    /**
     * Determine whether the user can delete a rental.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Alquiler $alquiler): bool
    {
        if ($alquiler->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('alquileres.delete');
    }

    /**
     * Determine whether the user can process a rental payment.
     */
    public function procesarPago(User $user, Alquiler $alquiler): bool
    {
        if ($alquiler->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('alquileres.edit');
    }
}
