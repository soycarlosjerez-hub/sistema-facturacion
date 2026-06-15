<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;
use Illuminate\Support\Facades\Cache;

class BusinessType extends Model
{
    protected $table = 'business_types';
    
    protected $fillable = [
        'key', 'slug', 'nombre', 'descripcion', 
        'color', 'color_default', 'icon', 'icono_default',
        'activo', 'orden', 'campos_extra', 'soft_delete_default'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
        'campos_extra' => 'array',
        'soft_delete_default' => 'boolean',
    ];

    public const CACHE_KEY = 'business_types_all';

    // Polymorphic relationship: categories that belong to this business type
    public function categories()
    {
        return $this->morphToMany(
            Category::class,
            'categorizable',
            'categorizables',
            'categorizable_id',  // FK on pivot to this model (business_types)
            'category_id'        // FK on pivot to related model (categories)
        )
            ->withPivot('configuracion', 'soft_delete_enabled')
            ->withTimestamps();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessTypeModule::class, 'business_type_id');
    }

    public function visibleModules()
    {
        return $this->modules()->where('visible', true)->orderBy('orden')->get();
    }

    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::with('modules')
                ->where('activo', true)
                ->orderBy('orden')
                ->get()
                ->keyBy('key')
                ->toArray();
        });
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function getActiveTypes(): array
    {
        $types = self::allCached();
        return array_map(function ($t) {
            return [
                'key' => $t['key'],
                'slug' => $t['slug'],
                'nombre' => $t['nombre'],
                'descripcion' => $t['descripcion'] ?? '',
                'color' => $t['color_default'] ?? $t['color'] ?? 'secondary',
                'icon' => $t['icono_default'] ?? $t['icon'] ?? 'bi-grid',
                'modulos' => collect($t['modules'] ?? [])
                    ->where('visible', true)
                    ->sortBy('orden')
                    ->pluck('modulo_key')
                    ->toArray(),
                'config' => $t['campos_extra'] ?? [],
            ];
        }, $types);
    }

    public static function getTipoActual(): ?string
    {
        return \App\Models\SystemSetting::get('tipo_negocio', 'mixto');
    }

    public static function getModulosVisibles(string $tipo = null): array
    {
        $tipo = $tipo ?? self::getTipoActual();
        $types = self::allCached();
        if (!isset($types[$tipo])) {
            return [];
        }
        return collect($types[$tipo]['modules'] ?? [])
            ->where('visible', true)
            ->sortBy('orden')
            ->pluck('modulo_key')
            ->toArray();
    }

    public static function isModuloVisible(string $moduloKey, string $tipo = null): bool
    {
        $visibles = self::getModulosVisibles($tipo);
        return in_array($moduloKey, $visibles);
    }

    protected static function booted(): void
    {
        static::saved(function () {
            self::flush();
            \App\Models\SystemSetting::flush();
        });

        static::deleted(function () {
            self::flush();
            \App\Models\SystemSetting::flush();
        });
    }
}