<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverEarningDetail extends Model
{
    use Auditable, TenantScope;

    protected $table = 'driver_earning_details';

    protected $fillable = [
        'tenant_id',
        'driver_earning_id',
        'orden_id',
        'venta_id',
        'monto_ganancia',
        'propina',
        'fecha',
    ];

    protected $casts = [
        'monto_ganancia' => 'decimal:2',
        'propina'        => 'decimal:2',
        'fecha'          => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function earning(): BelongsTo
    {
        return $this->belongsTo(DriverEarning::class, 'driver_earning_id');
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(DeliveryDriver::class);
    }
}
