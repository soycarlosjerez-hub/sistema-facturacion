<?php

namespace App\Policies;

use App\Models\Mesa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MesaPolicy
{
    /**
     * Determine whether the user can view any restaurant tables.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can view the table.
     * Enforces tenant isolation.
     */
    public function view(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can open a table (create order).
     */
    public function abrir(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can add items to an order.
     */
    public function agregarItem(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can remove items from an order.
     */
    public function quitarItem(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can process payment.
     */
    public function cobrar(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can update table status.
     */
    public function cambiarEstado(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can transfer an order between tables.
     */
    public function trasladar(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }

    /**
     * Determine whether the user can manage table positions (layout).
     */
    public function administrarLayout(User $user, Mesa $mesa): bool
    {
        if ($mesa->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('restaurante.view');
    }
}
