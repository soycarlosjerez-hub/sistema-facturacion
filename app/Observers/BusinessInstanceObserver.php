<?php

namespace App\Observers;

use App\Models\BusinessInstance;
use App\Models\User;

class BusinessInstanceObserver
{
    public function creating(BusinessInstance $instance): void
    {
        if ($instance->owner_user_id) {
            $owner = User::find($instance->owner_user_id);
            if ($owner) {
                $instance->owner_email = $owner->email;
                $instance->owner_nombre = $owner->name;
            }
        }
    }

    public function created(BusinessInstance $instance): void
    {
        if ($instance->owner_user_id && !$instance->owner_email) {
            $owner = User::find($instance->owner_user_id);
            if ($owner) {
                $instance->forceFill([
                    'owner_email' => $owner->email,
                    'owner_nombre' => $owner->name,
                ])->saveQuietly();
            }
        }
    }

    public function updating(BusinessInstance $instance): void
    {
        if ($instance->isDirty('owner_user_id') && $instance->owner_user_id) {
            $owner = User::find($instance->owner_user_id);
            if ($owner) {
                $instance->owner_email = $owner->email;
                $instance->owner_nombre = $owner->name;
            }
        }
    }

    public function updated(BusinessInstance $instance): void
    {
        if ($instance->wasChanged('owner_user_id') && $instance->owner_user_id) {
            $owner = User::find($instance->owner_user_id);
            if ($owner) {
                $instance->forceFill([
                    'owner_email' => $owner->email,
                    'owner_nombre' => $owner->name,
                ])->saveQuietly();
            }
        }

        if ($instance->owner_user_id && $instance->wasRecentlyCreated === false) {
            $staleOwner = User::find($instance->original['owner_user_id'] ?? null);
            $currentOwner = User::find($instance->owner_user_id);

            if ($staleOwner && $currentOwner && ($staleOwner->email !== $instance->owner_email || $staleOwner->name !== $instance->owner_nombre)) {
                $instance->forceFill([
                    'owner_email' => $currentOwner->email,
                    'owner_nombre' => $currentOwner->name,
                ])->saveQuietly();
            }
        }
    }

    public function deleted(BusinessInstance $instance): void
    {
        // No cleanup necesario, los datos cacheados se mantienen
    }

    public function restored(BusinessInstance $instance): void
    {
        // Si se restauró y no tiene datos de owner, poblarlos
        if (!$instance->owner_email && $instance->owner_user_id) {
            $owner = User::find($instance->owner_user_id);
            if ($owner) {
                $instance->forceFill([
                    'owner_email' => $owner->email,
                    'owner_nombre' => $owner->name,
                ])->saveQuietly();
            }
        }
    }
}
