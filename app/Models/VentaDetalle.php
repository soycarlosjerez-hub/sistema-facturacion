<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use TenantScope;

    protected $fillable = ['venta_id', 'producto_id','almacen_id', 'cantidad', 'precio_unitario', 'subtotal', 'notas', 'curso', 'estado_cocina', 'cocina_updated_at', 'tenant_id'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
}
