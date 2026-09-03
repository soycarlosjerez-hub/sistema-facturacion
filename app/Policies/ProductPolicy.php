<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any products.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('productos.view');
    }

    /**
     * Determine whether the user can view the product.
     * Enforces tenant isolation.
     */
    public function view(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.view');
    }

    /**
     * Determine whether the user can create products.
     */
    public function create(User $user): bool
    {
        return $user->can('productos.create');
    }

    /**
     * Determine whether the user can update the product.
     * Enforces tenant isolation.
     */
    public function update(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.edit');
    }

    /**
     * Determine whether the user can delete the product.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.delete');
    }

    /**
     * Determine whether the user can restore a soft-deleted product.
     */
    public function restore(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.delete');
    }

    /**
     * Determine whether the user can permanently delete a product.
     */
    public function forceDelete(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.delete');
    }

    /**
     * Determine whether the user can update stock (kardex operations).
     */
    public function updateStock(User $user, Producto $producto): bool
    {
        if ($producto->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('productos.edit');
    }
}
