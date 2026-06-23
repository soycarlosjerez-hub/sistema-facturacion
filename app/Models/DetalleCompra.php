<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompra extends Model
{
    use TenantScope;

    protected $table = 'compra_detalles';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'itbis_porcentaje',
        'subtotal',
        'tenant_id',
    ];

    protected $casts = [
        'cantidad'         => 'integer',
        'precio_unitario'  => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'subtotal'         => 'decimal:2',
    ];

    public function compra(): BelongsTo
    {
        return $this->belongsTo(Compra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getBaseAttribute(): float
    {
        return (float) $this->cantidad * (float) $this->precio_unitario;
    }

    public function getItbisMontoAttribute(): float
    {
        return round($this->base * ((float) $this->itbis_porcentaje / 100), 2);
    }
}
