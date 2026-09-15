<?php

namespace App\Services;

use App\Models\Backup;
use App\Models\BusinessInstance;
use App\Models\InstanceApiKey;
use App\Models\User;
use Illuminate\Support\Str;

class OwnerService
{
    /**
     * Sanitizar credenciales de base de datos para uso seguro en comandos.
     * Elimina caracteres peligrosos para shell injection.
     */
    public static function sanitizeDbCredentials(array $credentials): array
    {
        return [
            'host' => self::sanitizeHostname($credentials['host'] ?? '127.0.0.1'),
            'user' => self::sanitizeUsername($credentials['user'] ?? ''),
            'pass' => self::sanitizePassword($credentials['pass'] ?? ''),
            'name' => self::sanitizeDbName($credentials['name'] ?? ''),
        ];
    }

    /**
     * Sanitizar el hostname de la base de datos.
     */
    private static function sanitizeHostname(string $host): string
    {
        // Solo permitir hostnames válidos: letras, números, guiones, puntos
        return preg_replace('/[^a-zA-Z0-9.\-_]/', '', $host) ?: '127.0.0.1';
    }

    /**
     * Sanitizar el nombre de usuario de la base de datos.
     */
    private static function sanitizeUsername(string $user): string
    {
        // Solo permitir caracteres alfanuméricos y _-
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $user) ?: 'root';
    }

    /**
     * Sanitizar la contraseña de la base de datos.
     * Usa escapado seguro para evitar pérdida de caracteres.
     */
    private static function sanitizePassword(string $pass): string
    {
        // Escapar para usar como valor en archivo .cnf de MySQL
        // MySQL no necesita que se escapen caracteres especiales si se usa formato sin comillas
        // pero el archivo .cnf no debe tener saltos de línea ni nulos
        return str_replace(["\0"], '', $pass);
    }

    /**
     * Sanitizar el nombre de la base de datos.
     */
    private static function sanitizeDbName(string $name): string
    {
        // Solo permitir caracteres alfanuméricos, _ y -
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', $name) ?: 'sistema_facturacion';
    }

    /**
     * Sanitizar el nombre de archivo de backup.
     */
    public static function sanitizeBackupFilename(?string $filename): string
    {
        if (empty($filename)) {
            return '';
        }
        // Eliminar path traversal
        $filename = str_replace(['..', '/', '\\', ';', '`', '$', "'", '"'], '', $filename);
        // Solo permitir alfanuméricos, guiones, guión bajo, puntos y coma
        $filename = preg_replace('/[^a-zA-Z0-9._\-]/', '_', $filename);

        // Limitar longitud
        return substr($filename, 0, 100);
    }

    /**
     * Crear una InstanceApiKey de forma segura.
     */
    public static function createApiKey(BusinessInstance $instance, string $name, int $userId): InstanceApiKey
    {
        $rawKey = 'iak_'.Str::random(40);

        return InstanceApiKey::create([
            'business_instance_id' => $instance->id,
            'name' => $name,
            'key' => hash('sha256', $rawKey),
            'key_raw' => $rawKey,
            'is_active' => true,
            'created_by' => $userId,
        ]);
    }

    /**
     * Regenerar una API key existente.
     */
    public static function regenerateApiKey(InstanceApiKey $apiKey): string
    {
        $rawKey = 'iak_'.Str::random(40);
        $apiKey->update([
            'key' => hash('sha256', $rawKey),
            'key_raw' => $rawKey,
        ]);

        return $rawKey;
    }

    /**
     * Toggle (activar/desactivar) una API key.
     */
    public static function toggleApiKey(InstanceApiKey $apiKey): bool
    {
        return $apiKey->update(['is_active' => ! $apiKey->is_active]);
    }

    /**
     * Eliminar (soft delete) una API key.
     */
    public static function deleteApiKey(InstanceApiKey $apiKey): bool
    {
        return (bool) $apiKey->delete();
    }

    /**
     * Restaurar una API key eliminada (restore soft delete).
     */
    public static function restoreApiKey(InstanceApiKey $apiKey): bool
    {
        if (! $apiKey->trashed()) {
            return false;
        }

        return (bool) $apiKey->restore();
    }

    /**
     * Eliminar permanentemente una API key (hard delete).
     */
    public static function forceDeleteApiKey(InstanceApiKey $apiKey): bool
    {
        $apiKey->forceDelete();

        return true;
    }

    /**
     * Eliminar un owner y desvincular sus instancias.
     * Retorna la cantidad de instancias desvinculadas.
     */
    public static function deleteOwner(User $owner): int
    {
        $linkedInstances = BusinessInstance::where('owner_user_id', $owner->id)->count();

        // Revocar todos los tokens Sanctum
        Laravel\Sanctum\PersonalAccessToken::where('tokenable_type', get_class($owner))
            ->where('tokenable_id', $owner->id)
            ->delete();

        // Desvincular instancias
        if ($linkedInstances > 0) {
            BusinessInstance::where('owner_user_id', $owner->id)->update(['owner_user_id' => null]);
        }

        $owner->delete();

        return $linkedInstances;
    }

    /**
     * Obtener usuarios con rol owner desde caché.
     */
    public static function getOwnerUsers()
    {
        return cache()->remember('owner_users', 600, function () {
            return User::role('owner')->orderBy('name')->get();
        });
    }
}
