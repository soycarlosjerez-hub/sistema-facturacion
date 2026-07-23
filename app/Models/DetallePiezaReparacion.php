<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use App\Traits\TenantScope;

class DetallePiezaReparacion extends Model
{
    use HasFactory;
    use Auditable;
    use TenantScope;

    protected $table = 'detalle_pieza_reparacion';

    protected $fillable = [
        'tenant_id',
        'orden_reparacion_id',
        'producto_id',
        'cantidad',
        'costo_unitario',
        'precio_venta',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'costo_unitario' => 'decimal:2',
        'precio_venta' => 'decimal:2',
    ];

    public function ordenReparacion(): BelongsTo
    {
        return $this->belongsTo(OrdenReparacion::class, 'orden_reparacion_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
