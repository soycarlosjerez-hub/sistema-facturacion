<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DriverEarning extends Model
{
    use Auditable, TenantScope;

    protected $table = 'driver_earnings';

    protected $fillable = [
        'tenant_id',
        'driver_id',
        'periodo_inicio',
        'periodo_fin',
        'total_entregas',
        'total_ganancias',
    ];

    protected $casts = [
        'periodo_inicio'  => 'date',
        'periodo_fin'     => 'date',
        'total_entregas'  => 'integer',
        'total_ganancias' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(BusinessInstance::class, 'tenant_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(DeliveryDriver::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DriverEarningDetail::class);
    }

    // Accesor: suma de ganancias de todos los detalles (equivalente al 'base' por entrega)
    public function getMontoBaseAttribute()
    {
        return $this->attributes['monto_base']
            ?? ($this->details?->sum('monto_ganancia') ?? 0);
    }

    // Accesor: suma de propinas de todos los detalles
    public function getPropinasAttribute()
    {
        return $this->attributes['propinas']
            ?? ($this->details?->sum('propina') ?? 0);
    }

    // Accesor: comisión de plataforma (no se registra, siempre 0)
    public function getComisionPlataformaAttribute()
    {
        return 0;
    }

    // Accesor: nota (no existe en la tabla, siempre vacío)
    public function getNotaAttribute()
    {
        return '';
    }
}
