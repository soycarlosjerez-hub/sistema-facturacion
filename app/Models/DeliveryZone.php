<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    use Auditable, TenantScope;

    protected $table = 'delivery_zones';

    protected $fillable = [
        'tenant_id',
        'nombre',
        'descripcion',
        'radio_km',
        'tarifa_base',
        'tarifa_por_km',
        'tiempo_estimado_minutos',
        'zona_poligono',
        'minimo_para_envio_gratis',
        'activo',
    ];

    protected $casts = [
        'activo'                   => 'boolean',
        'zona_poligono'            => 'array',
        'radio_km'                 => 'decimal:2',
        'tarifa_base'              => 'decimal:2',
        'tarifa_por_km'            => 'decimal:2',
        'minimo_para_envio_gratis' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}
