<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Category extends Model
{
    use SoftDeletes, Auditable, \App\Traits\TenantScope;

    protected $table = 'categorias';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'activa',
        'color',
        'icono',
        'orden',
        'configuracion',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden' => 'integer',
        'configuracion' => 'array',
    ];

    // Polymorphic relationship: business types this category belongs to
    public function businessTypes()
    {
        return $this->morphedByMany(
            BusinessType::class,
            'categorizable',
            'categorizables',
            'category_id',
            'categorizable_id'
        )
            ->withPivot('configuracion', 'soft_delete_enabled')
            ->withTimestamps();
    }

    // Convenience: products in this category
    public function products(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Alias for backwards compatibility (old views use ->productos)
    public function productos(): HasMany
    {
        return $this->products();
    }

    // Convenience: tables in this category (restaurant)
    public function tables(): HasMany
    {
        return $this->hasMany(Mesa::class, 'categoria_id');
    }

    // Scope: filter by business type slug
    public function scopeOfType($query, string $typeSlug)
    {
        return $query->whereHas('businessTypes', function ($q) use ($typeSlug) {
            $q->where('business_types.slug', $typeSlug);
        });
    }

    // Scope: only active categories (alias for backwards compatibility)
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeActive($query)
    {
        return $query->where('activa', true);
    }

    // Scope: ordered by orden then nombre
    public function scopeOrdered($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    // Get merged configuration for a specific business type
    public function getConfigForType(string $typeSlug): array
    {
        $pivot = $this->businessTypes()->where('business_types.slug', $typeSlug)->first();
        $global = $this->configuracion ?? [];
        $typeConfig = $pivot?->pivot->configuracion ?? [];
        return array_merge($global, $typeConfig);
    }

    // Check if soft delete is enabled for a specific type
    public function hasSoftDeleteForType(string $typeSlug): bool
    {
        $pivot = $this->businessTypes()->where('business_types.slug', $typeSlug)->first();
        return $pivot?->pivot->soft_delete_enabled ?? true;
    }

    // Get color for a specific type (falls back to global, then type default)
    public function getColorForType(string $typeSlug): string
    {
        $config = $this->getConfigForType($typeSlug);
        if (isset($config['color'])) return $config['color'];
        if ($this->color) return $this->color;

        $type = BusinessType::where('slug', $typeSlug)->first();
        return $type?->color_default ?? $type?->color ?? '#3b82f6';
    }

    // Get icon for a specific type
    public function getIconForType(string $typeSlug): string
    {
        $config = $this->getConfigForType($typeSlug);
        if (isset($config['icono'])) return $config['icono'];
        if ($this->icono) return $this->icono;

        $type = BusinessType::where('slug', $typeSlug)->first();
        return $type?->icono_default ?? $type?->icon ?? 'bi-grid';
    }

    // Get orden for a specific type
    public function getOrdenForType(string $typeSlug): int
    {
        $config = $this->getConfigForType($typeSlug);
        if (isset($config['orden'])) return (int) $config['orden'];
        if (isset($this->orden)) return $this->orden;

        $pivot = $this->businessTypes()->where('business_types.slug', $typeSlug)->first();
        return $pivot?->pivot->orden ?? $this->orden ?? 0;
    }
}
