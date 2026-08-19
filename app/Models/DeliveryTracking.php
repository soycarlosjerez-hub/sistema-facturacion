<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use App\Models\BusinessInstance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
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

    /**
     * Acceso al tiempo estimado de la zona de delivery asociada.
     * Nota: No existe delivery_zone_id en 'ordenes' ni 'delivery_tracking'.
     * El valor siempre será 0 mientras no se agregue la columna a la tabla 'ordenes'.
     */
    public function getTiempoEstimadoMinutosAttribute()
    {
        return $this->deliveryZone?->tiempo_estimado_minutos ?? 0;
    }
}
