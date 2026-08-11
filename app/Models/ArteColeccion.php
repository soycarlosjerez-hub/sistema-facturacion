<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArteColeccion extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'tipo',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function obras(): HasMany
    {
        return $this->hasMany(ArteObra::class);
    }

    public function totalObras()
    {
        return $this->obras()->count();
    }
}