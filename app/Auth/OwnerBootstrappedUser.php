<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Representa al Owner autenticado via .env (Owner Bootstrap).
 *
 * Este usuario NO existe en la base de datos. Es una entidad "fantasma"
 * que permite al sistema reconocer al Owner incluso cuando la tabla
 * de usuarios estÃ¡ vacÃ­a.
 *
 * Se comporta como un Authenticatable de Laravel para que el sistema
 * de autenticaciÃ³n funcione sin modificar la arquitectura existente.
 */
class OwnerBootstrappedUser implements Authenticatable
{
    public function __construct(
        protected int $id,
        protected string $name,
        protected string $email,
        protected string $role,
    ) {
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthPasswordName()
    {
        return 'password';
    }

    public function getAuthPassword()
    {
        return '';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): ?string
    {
        return null;
    }

    /**
     * Obtener el email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Obtener el nombre.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Obtener el rol.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Verificar si tiene el rol dado.
     */
    public function hasRole(string $role): bool
    {
        // El Owner Bootstrap tiene todos los roles del sistema.
        // Esto garantiza bypass completo de permisos y roles.
        return true;
    }

    /**
     * Verificar si tiene alguno de los roles dados.
     */
    public function hasAnyRole(iterable $roles): bool
    {
        foreach ($roles as $r) {
            if ($this->hasRole($r)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si tiene todos los roles dados.
     */
    public function hasAllRoles(iterable $roles): bool
    {
        foreach ($roles as $r) {
            if (! $this->hasRole($r)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Owner Bootstrap tiene acceso total.
     */
    public function can($permission, $arguments = []): bool
    {
        return true;
    }

    public function __get(string $key): mixed
    {
        return match ($key) {
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'id' => $this->id,
            'is_bootstrap' => true,
            default => null,
        };
    }
}
