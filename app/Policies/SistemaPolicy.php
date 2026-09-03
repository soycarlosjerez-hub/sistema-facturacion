<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class SistemaPolicy
{
    /**
     * Determine whether the user can view system configuration.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('configuracion.view');
    }

    /**
     * Determine whether the user can view a specific system config.
     */
    public function view(User $user): bool
    {
        return $user->can('configuracion.view');
    }

    /**
     * Determine whether the user can update system configuration.
     * Only admin/owner levels.
     */
    public function update(User $user): bool
    {
        return $user->can('configuracion.edit');
    }

    /**
     * Determine whether the user can manage SMTP/mail configuration.
     */
    public function gestionarMail(User $user): bool
    {
        return $user->can('configuracion.edit');
    }

    /**
     * Determine whether the user can manage business type configuration.
     */
    public function gestionarBusinessType(User $user): bool
    {
        return $user->can('business-types.edit') || $user->can('configuracion.edit');
    }

    /**
     * Determine whether the user can manage NCF configurations.
     */
    public function gestionarNcf(User $user): bool
    {
        return $user->can('ncf.manage');
    }

    /**
     * Determine whether the user can manage e-CF certificates.
     */
    public function gestionarEcf(User $user): bool
    {
        return $user->can('ecf.certificados');
    }
}
