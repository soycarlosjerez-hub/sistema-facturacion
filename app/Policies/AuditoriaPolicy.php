<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuditoriaPolicy
{
    /**
     * Determine whether the user can view any audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('auditoria.view');
    }

    /**
     * Determine whether the user can view an audit log.
     */
    public function view(User $user, AuditLog $log): bool
    {
        return $user->can('auditoria.view');
    }

    /**
     * Determine whether the user can filter/export audit logs.
     */
    public function exportar(User $user): bool
    {
        return $user->can('auditoria.view');
    }
}
