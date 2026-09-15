<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarcaTecnologica extends Model
{
    use Auditable;
    use HasFactory;
    use TenantScope;

    protected $table = 'marca_tecnologicas';

    protected $fillable = [
        'nombre',
        'logo_url',
        'website',
        'pais',
        'contacto_email',
        'activo',
        'orden',
        'tenant_id',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    protected $appends = ['activo_label'];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'marca_tecnologica_id');
    }

    public function getActivoLabelAttribute(): string
    {
        return $this->activo ? 'Activo' : 'Inactivo';
    }
}
