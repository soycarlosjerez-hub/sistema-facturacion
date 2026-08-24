<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaSub extends Model
{
    use HasFactory, Auditable, TenantScope;

    protected $table = 'categoria_subs';

    protected $fillable = [
        'parent_id',
        'nombre',
        'descripcion',
        'activa',
        'orden',
        'tenant_id',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'orden'  => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeSinPadre($query)
    {
        return $query->whereNull('parent_id');
    }
}
