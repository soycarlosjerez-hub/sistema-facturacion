<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para reconstruir el Owner en la base de datos.
 *
 * Este servicio se usa cuando el Owner se autentica via Bootstrap
 * (desde .env) y necesita reconstruir su registro en la BD.
 */
class OwnerRecoveryService
{
    /**
     * Verificar si el Owner está autenticado (BD o Bootstrap).
     */
    public function isAuthenticated(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Usuario normal con rol owner
        try {
            if ($user->hasRole('owner') || $user->hasRole('root')) {
                return true;
            }
        } catch (\Throwable $e) {
            // Spatie puede fallar si las tablas no existen
            return false;
        }

        // Owner Bootstrap
        return isOwnerBootstrap();
    }

    /**
     * Reconstruir el Owner en la BD con los datos de .env.
     *
     * @return array{success: bool, message: string, user?: User}
     */
    public function rebuildOwner(string $password): array
    {
        if (! $this->isAuthenticated()) {
            return ['success' => false, 'message' => 'No autenticado como Owner.'];
        }

        $ownerBootstrap = app(OwnerBootstrapService::class);

        // Verificar si el Owner Bootstrap está habilitado
        if (! $ownerBootstrap->isEnabled()) {
            return ['success' => false, 'message' => 'Owner Bootstrap no está habilitado.'];
        }

        // Verificar si el Owner ya existe en BD
        $existing = User::where('email', $ownerBootstrap->getEmail())->first();

        if ($existing) {
            // Eliminar la sesión bootstrap
            session()->forget('owner_bootstrap');

            return [
                'success' => true,
                'isNew' => false,
                'message' => 'El Owner ya existe en la base de datos. No se requiere reconstrucción.',
                'user' => $existing,
            ];
        }

        // Crear el Owner en BD (sin audit log de bootstrap user)
        // Temporalmente deshabilitar la autenticación para evitar
        // que el audit log intente usar el ID -1 del bootstrap user.
        \Illuminate\Support\Facades\Auth::logout();

        $user = User::create([
            'name' => $ownerBootstrap->getName(),
            'email' => $ownerBootstrap->getEmail(),
            'password' => Hash::make($password),
            'role' => $ownerBootstrap->getRole(),
            'email_verified_at' => now(),
        ]);

        // Asignar rol Spatie
        try {
            $user->assignRole($ownerBootstrap->getRole());
        } catch (\Throwable $e) {
            Log::warning('Owner: no se pudo asignar rol Spatie al reconstruir. '.
                'Ejecutar: php artisan db:seed --class=RolesAndUsersSeeder', [
                'error' => $e->getMessage(),
            ]);
        }

        // Eliminar la sesión bootstrap
        session()->forget('owner_bootstrap');

        return [
            'success' => true,
            'isNew' => true,
            'message' => 'Owner reconstruido exitosamente en la base de datos.',
            'user' => $user,
        ];
    }

    /**
     * Cambiar la contraseña del Owner y mantenerlo sincronizado con .env.
     *
     * @return array{success: bool, message: string}
     */
    public function changePassword(string $currentPassword, string $newPassword, string $newPasswordConfirmation): array
    {
        if ($newPassword !== $newPasswordConfirmation) {
            return ['success' => false, 'message' => 'Las contraseñas nuevas no coinciden.'];
        }

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres.'];
        }

        // Verificar si es propietario de BD (no bootstrap)
        $existing = User::where('email', app(OwnerBootstrapService::class)->getEmail())->first();

        if (! $existing) {
            return ['success' => false, 'message' => 'Owner no existe en BD. Primero reconstruir el Owner.'];
        }

        if (! Hash::check($currentPassword, $existing->password)) {
            return ['success' => false, 'message' => 'La contraseña actual es incorrecta.'];
        }

        $existing->password = Hash::make($newPassword);
        $existing->save();

        Log::info('Owner: contraseña actualizada exitosamente. '.
            'Para actualizar el hash en .env, usar: '.
            'php -r "echo password_hash(\'nueva-password\', PASSWORD_BCRYPT);"', [
            'user_id' => $existing->id,
            'email' => $existing->email,
        ]);

        return [
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente. '
                . 'Se recomienda actualizar OWNER_PASSWORD_HASH en .env.',
        ];
    }

    /**
     * Verificar el estado actual del Owner (BD vs Bootstrap).
     *
     * @return array{authenticated: bool, isInDatabase: bool, isBootstrap: bool, mode: string}
     */
    public function getStatus(): array
    {
        $ownerBootstrap = app(OwnerBootstrapService::class);
        $inDb = $ownerBootstrap->ownerExistsInDatabase();

        return [
            'authenticated' => $this->isAuthenticated(),
            'is_in_database' => $inDb,
            'is_bootstrap' => isOwnerBootstrap(),
            'mode' => match (true) {
                isOwnerBootstrap() => 'bootstrap',
                $inDb => 'database',
                default => 'disabled',
            },
        ];
    }
}
