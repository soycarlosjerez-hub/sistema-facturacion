<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use App\Models\BusinessInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeliveryTracking extends Model
{
    use Auditable, TenantScope;

    protected $table = 'delivery_tracking';

    const STATUS_CREADO = 'creado';
    const STATUS_EN_CAMINO = 'en_camino';
    const STATUS_ENTREGADO = 'entregado';
    const STATUS_FALLIDO = 'fallido';
    const STATUS_CANCELADO = 'cancelado';

    protected $fillable = [
        'tenant_id',
        'orden_id',
        'venta_id',
        'driver_id',
        'status',
        'notas',
        'latitud',
        'longitud',
        'creado_por',
    ];

    protected $casts = [
        'latitud'  => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(DeliveryDriver::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function ventaZone()
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    public function getTiempoEstimadoMinutosAttribute()
    {
        return $this->ventaZone?->tiempo_estimado_minutos ?? 0;
    }
}
