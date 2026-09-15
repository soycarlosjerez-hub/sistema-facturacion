<?php

/**
 * Verificar si el usuario autenticado actual es el Owner Bootstrap.
 *
 * Retorna true si:
 *  - El usuario logueado es una instancia de OwnerBootstrappedUser
 *  - O la sesión contiene el flag 'owner_bootstrap' => true
 */
if (! function_exists('isOwnerBootstrap')) {
    function isOwnerBootstrap(): bool
    {
        $user = auth()->user();

        if ($user instanceof \App\Auth\OwnerBootstrappedUser) {
            return true;
        }

        return (bool) session()->get('owner_bootstrap', false);
    }
}

/**
 * Verificar si el usuario autenticado tiene el rol Owner (normal o Bootstrap).
 */
if (! function_exists('isOwner')) {
    function isOwner(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        try {
            if ($user instanceof \App\Auth\OwnerBootstrappedUser) {
                return $user->hasRole('owner');
            }

            return $user->hasRole('owner') || $user->hasRole('root');
        } catch (\Throwable $e) {
            return false;
        }
    }
}

/**
 * Obtener si el Owner Bootstrap existe en BD o está en modo "fantasma".
 *
 * @return array{exists: bool, mode: string}
 *   - exists: true si el usuario existe en BD
 *   - mode: 'bd' | 'bootstrap'
 */
if (! function_exists('ownerBootstrapStatus')) {
    function ownerBootstrapStatus(): array
    {
        if (! isOwnerBootstrap()) {
            return ['exists' => false, 'mode' => 'none'];
        }

        $service = app(\App\Services\OwnerBootstrapService::class);
        $inDb = $service->ownerExistsInDatabase();

        return [
            'exists' => $inDb,
            'mode' => $inDb ? 'bd' : 'bootstrap',
        ];
    }
}
