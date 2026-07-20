<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Model;

class OrdenDetalle extends Model
{
    use TenantScope;

    protected $casts = [
        'cantidad'          => 'decimal:2',
        'precio_unitario'   => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'cocina_updated_at' => 'datetime',
    ];

    protected $fillable = [
        'orden_id', 'producto_id', 'almacen_id',
        'cantidad', 'precio_unitario', 'subtotal',
        'notas', 'curso', 'estado_cocina',
        'cocina_updated_at', 'tenant_id',
    ];

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
}
