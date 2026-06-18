<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'description', 'tenant_id'];

    public const CACHE_TTL = 3600;

    /**
     * Get the current tenant identifier.
     * Returns null for global settings (owner/root).
     */
    public static function tenantId(): ?int
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        // Owners and root have access to global settings
        if ($user->hasRole('owner') || $user->hasRole('root')) {
            return null;
        }
        return $user->business_instance_id ?? null;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();
        return $settings[$key] ?? $default;
    }

    public static function allCached(): array
    {
        $tenantId = static::tenantId();
        $cacheKey = $tenantId ? 'system_settings_all_' . $tenantId : 'system_settings_all_global';
        return Cache::remember($cacheKey, static::CACHE_TTL, function () use ($tenantId) {
            $query = static::query()->select('key', 'value');
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            } else {
                $query->whereNull('tenant_id');
            }
            return $query->pluck('value', 'key')->all();
        });
    }

    public static function flush(): void
    {
        $tenantId = static::tenantId();
        $cacheKey = $tenantId ? 'system_settings_all_' . $tenantId : 'system_settings_all_global';
        Cache::forget($cacheKey);
    }

    public static function empresaNombre(): string
    {
        return static::get('empresa_nombre', 'Mi Negocio');
    }

    public static function empresaSlogan(): string
    {
        return static::get('sistema_slogan', 'Sistema de Ventas');
    }

    public static function monedaSimbolo(): string
    {
        return static::get('moneda_simbolo', 'RD$');
    }

    public static function itbisDefault(): float
    {
        return (float) static::get('impuesto_itbis', 18);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            static::flush();
        });

        static::deleted(function () {
            static::flush();
        });
    }
}
