<?php

namespace App\Policies;

use App\Models\BusinessInstance;
use App\Models\InstanceApiKey;
use App\Models\User;

class InstanceApiKeyPolicy
{
    public function viewAny(User $user, BusinessInstance $businessInstance): bool
    {
        return $user->can('owner.instances.view') && $this->hasAccess($user, $businessInstance);
    }

    public function create(User $user, BusinessInstance $businessInstance): bool
    {
        return $user->can('owner.instances.edit') && $this->hasAccess($user, $businessInstance);
    }

    public function update(User $user, InstanceApiKey $apiKey): bool
    {
        return $this->hasAccess($user, $apiKey->instance) && $user->can('owner.instances.edit');
    }

    public function delete(User $user, InstanceApiKey $apiKey): bool
    {
        return $this->hasAccess($user, $apiKey->instance) && $user->can('owner.instances.edit');
    }

    public function forceDelete(User $user, InstanceApiKey $apiKey): bool
    {
        return $this->hasAccess($user, $apiKey->instance) && $user->can('owner.instances.edit');
    }

    public function view(User $user, InstanceApiKey $apiKey): bool
    {
        return $this->hasAccess($user, $apiKey->instance) && $user->can('owner.instances.edit');
    }

    private function hasAccess(User $user, ?BusinessInstance $instance): bool
    {
        if (! $instance) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        return (string) $instance->owner_user_id === (string) $user->id
            || $user->business_instance_id === $instance->id;
    }
}
