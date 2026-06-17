<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    public const CACHE_KEY = 'system_settings_all';
    public const CACHE_TTL = 3600;

    public static function get(string $key, ?string $default = null): ?string
    {
        $settings = static::allCached();
        return $settings[$key] ?? $default;
    }

    public static function allCached(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return static::pluck('value', 'key')->all();
        });
    }

    public static function flush(): void
    {
        Cache::forget(static::CACHE_KEY);
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
