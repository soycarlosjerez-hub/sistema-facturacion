<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('categorias.view');
    }

    public function view(User $user, Category $category): bool
    {
        if ($category->tenant_id !== $user->tenant_id) {
            return false;
        }
        return $user->can('categorias.view');
    }

    public function create(User $user): bool
    {
        return $user->can('categorias.create');
    }

    public function update(User $user, Category $category): bool
    {
        if ($category->tenant_id !== $user->tenant_id) {
            return false;
        }
        return $user->can('categorias.edit');
    }

    public function delete(User $user, Category $category): bool
    {
        if ($category->tenant_id !== $user->tenant_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }

    public function restore(User $user, Category $category): bool
    {
        if ($category->tenant_id !== $user->tenant_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }

    public function forceDelete(User $user, Category $category): bool
    {
        if ($category->tenant_id !== $user->tenant_id) {
            return false;
        }
        return $user->can('categorias.delete');
    }
}