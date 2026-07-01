<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenProducto extends Model
{
    use HasFactory;

    protected $fillable = [
        'almacen_id',
        'producto_id',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'costo_promedio',
    ];

    protected $casts = [
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'costo_promedio' => 'decimal:2',
    ];

    public function almacen(): BelongsTo
    {
        return $this->belongsTo(Almacen::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
