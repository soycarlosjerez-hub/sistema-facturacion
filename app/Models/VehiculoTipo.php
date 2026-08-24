<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehiculoTipo extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'vehiculo_tipos';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'slug',
        'icono',
        'color',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];

    /**
     * Vehículos de este tipo
     */
    public function vehiculos(): HasMany
    {
        return $this->hasMany(Vehiculo::class, 'tipo_id');
    }

    /**
     * scope para solo activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
