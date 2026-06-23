<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\TenantScope;

class CotizacionItem extends Model
{
    use HasFactory;
    use TenantScope;

    protected $table = 'cotizacion_items';

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'codigo',
        'nombre',
        'descripcion',
        'unidad',
        'cantidad',
        'precio_unitario',
        'descuento',
        'itbis_porcentaje',
        'itbis',
        'subtotal',
        'total',
        'orden',
        'tenant_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'itbis' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Calcular valores del item
    public function calcular(): void
    {
        $subtotal = ($this->cantidad * $this->precio_unitario) - $this->descuento;
        $this->itbis = round($subtotal * ($this->itbis_porcentaje / 100), 2);
        $this->subtotal = round($subtotal, 2);
        $this->total = round($subtotal + $this->itbis, 2);
    }
}
