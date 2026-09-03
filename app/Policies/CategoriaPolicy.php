<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Alias policy for Categoria (alias of Category).
 * Enforces tenant isolation + Spatie permissions.
 */
class CategoriaPolicy
{
    /**
     * Determine whether the user can view any categories.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('categorias.view');
    }

    /**
     * Determine whether the user can view the category.
     * Enforces tenant isolation.
     */
    public function view(User $user, Category $categoria): bool
    {
        if ($categoria->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('categorias.view');
    }

    /**
     * Determine whether the user can create categories.
     */
    public function create(User $user): bool
    {
        return $user->can('categorias.create');
    }

    /**
     * Determine whether the user can update the category.
     * Enforces tenant isolation.
     */
    public function update(User $user, Category $categoria): bool
    {
        if ($categoria->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('categorias.edit');
    }

    /**
     * Determine whether the user can delete the category.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Category $categoria): bool
    {
        if ($categoria->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }

    /**
     * Determine whether the user can restore a soft-deleted category.
     */
    public function restore(User $user, Category $categoria): bool
    {
        if ($categoria->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }

    /**
     * Determine whether the user can permanently delete a category.
     */
    public function forceDelete(User $user, Category $categoria): bool
    {
        if ($categoria->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }
}
