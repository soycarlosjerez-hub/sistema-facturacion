<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LavaderoPaquete extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'lavadero_paquetes';

    protected $fillable = [
        'business_type_id',
        'tenant_id',
        'sucursal_id',
        'nombre',
        'descripcion',
        'precio',
        'precio_anterior',
        'duracion_minutos',
        'aplicable_a_tipo',
        'activo',
        'max_usos_cliente',
        'veces_usado',
        'orden',
        'configuracion',
        'tags',
    ];

    protected $casts = [
        'precio'          => 'decimal:2',
        'precio_anterior' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'activo'          => 'boolean',
        'max_usos_cliente' => 'integer',
        'veces_usado'     => 'integer',
        'orden'           => 'integer',
        'configuracion'   => 'array',
        'tags'            => 'array',
    ];

    /**
     * Tipo de negocio (lavadero, restaurante, etc.)
     */
    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    /**
     * Items/servicios que conforman el paquete
     */
    public function items(): HasMany
    {
        return $this->hasMany(LavaderoPaqueteItem::class, 'paquete_id');
    }

    /**
     * Sucursal donde se aplica el paquete
     */
    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    /**
     * scope para solo activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
