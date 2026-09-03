<?php

namespace App\Policies;

use App\Models\Ecf;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EcfPolicy
{
    /**
     * Determine whether the user can view any e-CF documents.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ecf.view');
    }

    /**
     * Determine whether the user can view the e-CF.
     * Enforces tenant isolation.
     */
    public function view(User $user, Ecf $ecf): bool
    {
        if ($ecf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ecf.view');
    }

    /**
     * Determine whether the user can manage e-CF (send to DGII).
     */
    public function gestionar(User $user): bool
    {
        return $user->can('ecf.manage');
    }

    /**
     * Determine whether the user can generate an e-CF for a sale.
     * Enforces tenant isolation.
     */
    public function generar(User $user, Ecf $ecf): bool
    {
        if ($ecf->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('ecf.manage');
    }

    /**
     * Determine whether the user can manage digital certificates.
     */
    public function verCertificados(User $user): bool
    {
        return $user->can('ecf.certificados');
    }
}
