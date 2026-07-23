<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TicketGarantia;
use Illuminate\Auth\Access\Response;

class TicketGarantiaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_climatizacion::ticket_garantia');
    }

    public function view(User $user, TicketGarantia $ticket): bool
    {
        return $user->can('view_climatizacion::ticket_garantia');
    }

    public function create(User $user): bool
    {
        return $user->can('create_climatizacion::ticket_garantia');
    }

    public function update(User $user, TicketGarantia $ticket): bool
    {
        return $user->can('update_climatizacion::ticket_garantia');
    }

    public function delete(User $user, TicketGarantia $ticket): bool
    {
        return $user->can('delete_climatizacion::ticket_garantia');
    }
}
