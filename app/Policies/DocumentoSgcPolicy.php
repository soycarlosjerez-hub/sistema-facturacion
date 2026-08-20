<?php

namespace App\Policies;

use App\Models\User;

class DocumentoSgcPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, $doc): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, $doc): bool
    {
        return $user->hasRole('admin-business', 'gerente', 'root')
            || $doc->creado_por === $user->id;
    }

    public function delete(User $user, $doc): bool
    {
        return $user->hasRole('admin-business', 'gerente', 'root')
            || $doc->creado_por === $user->id;
    }

    public function aprobar(User $user, $doc): bool
    {
        return $user->hasRole('admin-business', 'gerente', 'root');
    }

    public function descargar(User $user, $doc): bool
    {
        return true;
    }
}
