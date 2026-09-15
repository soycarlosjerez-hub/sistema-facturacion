<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OwnerBootstrapService
{
    /**
     * Verificar si el Owner Bootstrap esta habilitado.
     */
    public function isEnabled(): bool
    {
        return (bool) config('owner.enabled', false);
    }

    /**
     * Verificar si el Owner Bootstrap tiene un hash de contraseña definido.
     */
    public function hasPassword(): bool
    {
        $hash = config('owner.password_hash', '');

        if (empty($hash)) {
            return false;
        }

        return $this->isValidBcryptHash($hash);
    }

    /**
     * Obtener el email del Owner Bootstrap.
     */
    public function getEmail(): string
    {
        return (string) config('owner.email', 'owner@sistema-facturacion.com');
    }

    /**
     * Obtener el hash bcrypt de la contraseÃ±a del Owner desde .env.
     *
     * Opcion RECOMENDADA: configurar OWNER_PASSWORD_HASH con un valor
     * generado por: php -r "echo password_hash('tu-password', PASSWORD_BCRYPT);"
     *
     * Esto garantiza que la contraseÃ±a NUNCA quede en texto plano en el
     * servidor.
     */
    public function getPasswordHash(): string
    {
        return (string) config('owner.password_hash', '');
    }

    /**
     * Obtener la contraseÃ±a en texto plano desde .env (legacy/alternativa).
     *
     * DEPRECATED: Preferir OWNER_PASSWORD_HASH para mayor seguridad.
     * La contraseÃ±a en texto plano SI se almacena en el .env del servidor.
     */
    public function getPassword(): string
    {
        return (string) config('owner.password', '');
    }

    /**
     * Verificar si se usa el campo password_hash (secure mode).
     */
    public function usePasswordHash(): bool
    {
        return (bool) config('owner.use_password_hash', false);
    }

    /**
     * Verificar si un string es un hash bcrypt válido.
     */
    protected function isValidBcryptHash(string $hash): bool
    {
        return str_starts_with($hash, '$2') && strlen($hash) >= 50;
    }

    /**
     * Obtener el nombre del Owner.
     */
    public function getName(): string
    {
        return (string) config('owner.name', 'Owner');
    }

    /**
     * Obtener el rol Spatie del Owner.
     */
    public function getRole(): string
    {
        return (string) config('owner.role', 'owner');
    }

    /**
     * Obtener si se debe omitir 2FA para el Owner Bootstrap.
     */
    public function shouldBypass2FA(): bool
    {
        return (bool) config('owner.bypass_2fa', false);
    }

    /**
     * Intentar autenticar con las credenciales del Owner Bootstrap.
     *
     * @return array{success: bool, isBootstrap: bool, message?: string}
     */
    public function authenticate(string $email, string $password): array
    {
        // Solo autenticar si el email coincide EXACTAMENTE con el configurado
        if (strtolower(trim($email)) !== strtolower(trim($this->getEmail()))) {
            return ['success' => false, 'isBootstrap' => false];
        }

        // Verificar el hash bcrypt primero (modo seguro)
        $hash = $this->getPasswordHash();

        if ($hash && $this->isValidBcryptHash($hash) && Hash::check($password, $hash)) {
            return ['success' => true, 'isBootstrap' => true];
        }

        // Si el hash no es válido bcrypt, ignorarlo (no lanzar excepción)
        if ($hash && ! $this->isValidBcryptHash($hash)) {
            Log::warning('Owner Bootstrap: hash de contraseña no válido. Asegúrese de usar un hash bcrypt. ', [
                'hash_prefix' => substr($hash, 0, 7),
            ]);
        }

        // Fallback: contraseña en texto plano (legacy)
        $plain = $this->getPassword();
        if ($plain && $password === $plain) {
            return ['success' => true, 'isBootstrap' => true];
        }

        Log::warning('Owner Bootstrap: intento fallido de contraseÃ±a.', [
            'email' => $email,
            'source_ip' => request()->ip(),
        ]);

        return ['success' => false, 'isBootstrap' => true];
    }

    /**
     * Verificar si el email coincide con el Owner Bootstrap configurado.
     */
    public function isBootstrapEmail(string $email): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        return strtolower(trim($email)) === strtolower(trim($this->getEmail()));
    }

    /**
     * Verificar si el Owner existe en la base de datos.
     */
    public function ownerExistsInDatabase(): bool
    {
        return User::where('email', $this->getEmail())->exists();
    }

    /**
     * Obtener el registro del Owner desde la BD (si existe).
     */
    public function getOwnerFromDatabase(): ?User
    {
        return User::where('email', $this->getEmail())->first();
    }

    /**
     * Reconstruir el Owner en la BD con los datos de .env.
     *
     * Este mÃ©todo debe ser llamado SOLO cuando el Owner se autentica
     * via Bootstrap y necesita reconstruir su registro en la BD.
     */
    public function rebuildOwnerInDatabase(string $password, ?string $role = null): User
    {
        $existing = User::where('email', $this->getEmail())->first();

        if ($existing) {
            return $existing;
        }

        $roleName = $role ?? $this->getRole();

        $user = User::create([
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => Hash::make($password),
            'role' => $roleName,
            'email_verified_at' => now(),
        ]);

        // Asignar rol Spatie (puede fallar si la tabla de roles no existe)
        try {
            $user->assignRole($roleName);
        } catch (\Throwable $e) {
            // El rol o las tablas Spatie pueden no existir si la BD
            // fue recien creada. El Owner puede ejecutar el seeder
            // despues de reconstruir.
            Log::warning('Owner Bootstrap: no se pudo asignar rol Spatie al reconstruir. '.
                'Ejecutar: php artisan db:seed --class=RolesAndUsersSeeder', [
                'error' => $e->getMessage(),
            ]);
        }

        return $user;
    }
}
