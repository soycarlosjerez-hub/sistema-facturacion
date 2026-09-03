<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClientePolicy
{
    /**
     * Determine whether the user can view any clients.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('clientes.view');
    }

    /**
     * Determine whether the user can view the client.
     * Enforces tenant isolation.
     */
    public function view(User $user, Cliente $cliente): bool
    {
        if ($cliente->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('clientes.view');
    }

    /**
     * Determine whether the user can create clients.
     */
    public function create(User $user): bool
    {
        return $user->can('clientes.create');
    }

    /**
     * Determine whether the user can update the client.
     * Enforces tenant isolation.
     */
    public function update(User $user, Cliente $cliente): bool
    {
        if ($cliente->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('clientes.edit');
    }

    /**
     * Determine whether the user can delete the client.
     * Enforces tenant isolation.
     */
    public function delete(User $user, Cliente $cliente): bool
    {
        if ($cliente->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('clientes.delete');
    }

    /**
     * Determine whether the user can restore a soft-deleted client.
     */
    public function restore(User $user, Cliente $cliente): bool
    {
        if ($cliente->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('clientes.delete');
    }

    /**
     * Determine whether the user can permanently delete a client.
     */
    public function forceDelete(User $user, Cliente $cliente): bool
    {
        if ($cliente->business_instance_id !== $user->business_instance_id) {
            return false;
        }
        return $user->can('clientes.delete');
    }
}
