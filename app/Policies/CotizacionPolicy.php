<?php

namespace App\Policies;

use App\Models\Cotizacion;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CotizacionPolicy
{
    /**
     * Determine whether the user can view any quotes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('cotizaciones.view');
    }

    /**
     * Determine whether the user can view the quote.
     * Enforces tenant isolation.
     */
    public function view(User $user, Cotizacion $cotizacion): bool
    {
        if ($cotizacion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cotizaciones.view');
    }

    /**
     * Determine whether the user can create quotes.
     */
    public function create(User $user): bool
    {
        return $user->can('cotizaciones.create');
    }

    /**
     * Determine whether the user can update the quote.
     * Enforces tenant isolation.
     */
    public function update(User $user, Cotizacion $cotizacion): bool
    {
        if ($cotizacion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cotizaciones.edit');
    }

    /**
     * Determine whether the user can delete the quote.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Cotizacion $cotizacion): bool
    {
        if ($cotizacion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cotizaciones.delete');
    }

    /**
     * Determine whether the user can convert quote to sale.
     */
    public function convertirVenta(User $user, Cotizacion $cotizacion): bool
    {
        if ($cotizacion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cotizaciones.edit');
    }

    /**
     * Determine whether the user can send quote by email.
     */
    public function enviarEmail(User $user, Cotizacion $cotizacion): bool
    {
        if ($cotizacion->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('cotizaciones.view');
    }
}
