<?php

namespace App\Models;

use App\Traits\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = ['venta_id', 'producto_id', 'obra_id', 'equipo_id', 'almacen_id', 'cantidad', 'precio_unitario', 'subtotal', 'descuento', 'descuento_tipo', 'itbis_porcentaje', 'sin_itbis', 'notas', 'curso', 'estado_cocina', 'cocina_updated_at', 'tenant_id'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'sin_itbis' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function obra()
    {
        return $this->belongsTo(ArteObra::class, 'obra_id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
