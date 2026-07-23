<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class InstalacionEquipoDomotico extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'instalacion_equipo_domotico';

    protected $fillable = [
        'tenant_id',
        'servicio_domotica_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'ubicacion_instalacion',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    public function servicioDomotica(): BelongsTo
    {
        return $this->belongsTo(ServicioDomotica::class, 'servicio_domotica_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getEstadoLabelAttribute(): ?string
    {
        return match ($this->estado) {
            'instaldo' => 'Instalado',
            'pendiente' => 'Pendiente',
            'fallido' => 'Fallido',
            'cancelado' => 'Cancelado',
            default => null,
        };
    }
}
