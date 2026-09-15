<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = ['clave', 'grupo', 'valor', 'tipo', 'descripcion', 'tenant_id'];

    public function getAttribute($key)
    {
        return match ($key) {
            'key' => $this->getAttributeValue('clave'),
            'value' => $this->getAttributeValue('valor'),
            default => parent::getAttribute($key),
        };
    }

    public function getAttributes()
    {
        $attributes = parent::getAttributes();
        unset($attributes['key'], $attributes['value']);

        return $attributes;
    }

    public function setAttribute($key, $value)
    {
        return parent::setAttribute(match ($key) {
            'key' => 'clave',
            'value' => 'valor',
            default => $key,
        }, $value);
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

    public const CACHE_TTL = 3600;

    public static function tenantId(): ?int
    {
        $user = Auth::user();
        if (! $user) {
            return null;
        }
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
        $cacheKey = $tenantId ? 'system_settings_all_'.$tenantId : 'system_settings_all_global';

        return Cache::remember($cacheKey, static::CACHE_TTL, function () use ($tenantId) {
            $query = static::query()->select('clave', 'valor');
            if ($tenantId) {
                $query->where('tenant_id', $tenantId);
            } else {
                $query->whereNull('tenant_id');
            }

            return $query->pluck('valor', 'clave')->all();
        });
    }

    public static function flush(): void
    {
        $tenantId = static::tenantId();
        $cacheKey = $tenantId ? 'system_settings_all_'.$tenantId : 'system_settings_all_global';
        Cache::forget($cacheKey);
    }

    public static function empresaNombre(): string
    {
        return static::get('empresa_nombre', 'Mi Negocio');
    }

    public static function nombreEmpresaActual(): string
    {
        $user = Auth::user();
        if ($user && $user->business_instance_id) {
            $instance = BusinessInstance::find($user->business_instance_id);
            if ($instance && $instance->nombre) {
                return $instance->nombre;
            }
        }

        return static::empresaNombre();
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
}
