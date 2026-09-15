<?php

namespace App\Auth;

use App\Models\User;
use App\Services\OwnerBootstrapService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

/**
 * Provider de autenticacion que combina:
 *  1. Busqueda de usuario en la BD (User model)
 *  2. Fallback a Owner Bootstrap cuando la BD no tiene el usuario
 *
 * Este provider se usa como alternativa al provider 'users' definido
 * en config/auth.php. Se activa manualmente en los controladores
 * cuando se necesita autenticacion via .env.
 */
class OwnerBootstrapProvider implements UserProvider
{
    public function __construct(
        protected OwnerBootstrapService $ownerBootstrap
    ) {
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        return User::find($identifier);
    }

    public function retrieveByIdentity($identifier): ?Authenticatable
    {
        return User::where('email', $identifier)->first();
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $email = $credentials['email'] ?? '';
        $password = $credentials['password'] ?? '';

        // 1. Primero intentar con BD
        $user = User::where('email', $email)->first();

        if ($user) {
            return $user;
        }

        // 2. Si no existe en BD, verificar si corresponde al Owner Bootstrap
        if ($this->ownerBootstrap->isEnabled()) {
            $result = $this->ownerBootstrap->authenticate($email, $password);

            if (($result['success'] ?? false) && ! ($result['locked'] ?? false)) {
                // Devolver un usuario "fantasma" (no persistido en BD)
                return new OwnerBootstrappedUser(
                    config('owner.email'),
                    config('owner.name'),
                    config('owner.role', 'owner'),
                    ! ($result['plaintext'] ?? false)
                );
            }
        }

        return null;
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        // Usuario normal (BD)
        if ($user instanceof User) {
            return Hash::check($credentials['password'], $user->password);
        }

        // Usuario Owner Bootstrap
        if ($user instanceof OwnerBootstrappedUser) {
            return $user->isValidated();
        }

        return false;
    }

    public function refreshModel(Authenticatable $user): ?Authenticatable
    {
        if ($user instanceof User) {
            return User::find($user->getAuthIdentifier());
        }

        return $user;
    }

    public function updateRememberToken(Authenticatable $user, ?string $token): void
    {
        // No aplica para owner bootstrap
    }
}
