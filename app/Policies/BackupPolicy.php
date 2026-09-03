<?php

namespace App\Policies;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BackupPolicy
{
    /**
     * Determine whether the user can view any backups.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('backups.view');
    }

    /**
     * Determine whether the user can view a backup.
     */
    public function view(User $user, Backup $backup): bool
    {
        return $user->can('backups.view');
    }

    /**
     * Determine whether the user can create a backup.
     */
    public function create(User $user): bool
    {
        return $user->can('backups.create');
    }

    /**
     * Determine whether the user can restore a backup.
     */
    public function restaurar(User $user, Backup $backup): bool
    {
        return $user->can('backups.restore');
    }

    /**
     * Determine whether the user can delete a backup.
     */
    public function delete(User $user, Backup $backup): bool
    {
        return $user->can('backups.delete');
    }
}
