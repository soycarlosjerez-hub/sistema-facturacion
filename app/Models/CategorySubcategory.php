<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategorySubcategory extends Model
{
    use HasFactory;

    protected $table = 'category_subcategories';

    protected $fillable = [
        'category_id',
        'business_type_id',
        'parent_id',
        'nombre',
        'orden',
        'activa',
        'configuracion',
    ];

    protected $casts = [
        'activa'     => 'boolean',
        'orden'      => 'integer',
        'configuracion' => 'array',
    ];

    /**
     * Categoría padre (ej. "Alimentos", "Bebidas", "Accesorios")
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Tipo de negocio (lavadero, restaurante, etc.)
     */
    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    /**
     * Subcategoría padre (relación jerárquica)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CategorySubcategory::class, 'parent_id');
    }

    /**
     * Subcategorías hijas (relación jerárquica)
     */
    public function children(): HasMany
    {
        return $this->hasMany(CategorySubcategory::class, 'parent_id');
    }

    /**
     * scope para solo activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * scope para un tipo de negocio
     */
    public function scopeBusinessType($query, int $businessTypeId)
    {
        return $query->where('business_type_id', $businessTypeId);
    }
}
