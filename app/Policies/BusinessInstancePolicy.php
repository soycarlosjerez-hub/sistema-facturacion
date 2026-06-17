<?php

namespace App\Policies;

use App\Models\BusinessInstance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BusinessInstancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('owner.instances.view') || $user->can('owner.dashboard');
    }

    public function view(User $user, BusinessInstance $businessInstance): bool
    {
        if ($user->hasRole('owner')) {
            return $user->can('owner.instances.view');
        }
        return $user->business_instance_id === $businessInstance->id && $user->can('owner.instances.view');
    }

    public function create(User $user): bool
    {
        return $user->can('owner.instances.create');
    }

    public function update(User $user, BusinessInstance $businessInstance): bool
    {
        return $user->can('owner.instances.edit');
    }

    public function delete(User $user, BusinessInstance $businessInstance): bool
    {
        return $user->can('owner.instances.delete');
    }

    public function config(User $user, BusinessInstance $businessInstance): bool
    {
        return $user->can('owner.instances.config');
    }
}
