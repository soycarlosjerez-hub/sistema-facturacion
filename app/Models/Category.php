<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Category extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'categories';

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
            'category_id',      // FK on pivot to this model (categories)
            'categorizable_id'  // FK on pivot to related model (business_types)
        )
            ->withPivot('configuracion', 'soft_delete_enabled')
            ->withTimestamps();
    }

    // Convenience: products in this category
    public function products(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    // Convenience: tables in this category (restaurant)
    public function tables(): HasMany
    {
        return $this->hasMany(Mesa::class, 'categoria_id');
    }

    // Scope: filter by business type key
    public function scopeOfType($query, string $typeKey)
    {
        return $query->whereHas('businessTypes', function ($q) use ($typeKey) {
            $q->where('business_types.key', $typeKey);
        });
    }

    // Scope: only active categories
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
    public function getConfigForType(string $typeKey): array
    {
        $pivot = $this->businessTypes()->where('business_types.key', $typeKey)->first();
        $global = $this->configuracion ?? [];
        $typeConfig = $pivot?->pivot->configuracion ?? [];
        return array_merge($global, $typeConfig);
    }

    // Check if soft delete is enabled for a specific type
    public function hasSoftDeleteForType(string $typeKey): bool
    {
        $pivot = $this->businessTypes()->where('business_types.key', $typeKey)->first();
        return $pivot?->pivot->soft_delete_enabled ?? true;
    }

    // Get color for a specific type (falls back to global, then type default)
    public function getColorForType(string $typeKey): string
    {
        $config = $this->getConfigForType($typeKey);
        if (isset($config['color'])) return $config['color'];
        if ($this->color) return $this->color;
        
        $type = BusinessType::where('key', $typeKey)->first();
        return $type?->color_default ?? $type?->color ?? '#3b82f6';
    }

    // Get icon for a specific type
    public function getIconForType(string $typeKey): string
    {
        $config = $this->getConfigForType($typeKey);
        if (isset($config['icono'])) return $config['icono'];
        if ($this->icono) return $this->icono;
        
        $type = BusinessType::where('key', $typeKey)->first();
        return $type?->icono_default ?? $type?->icon ?? 'bi-grid';
    }

    // Get orden for a specific type
    public function getOrdenForType(string $typeKey): int
    {
        $config = $this->getConfigForType($typeKey);
        if (isset($config['orden'])) return (int) $config['orden'];
        if (isset($this->orden)) return $this->orden;
        
        $pivot = $this->businessTypes()->where('business_types.key', $typeKey)->first();
        return $pivot?->pivot->orden ?? $this->orden ?? 0;
    }
}