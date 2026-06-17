<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingrediente extends Model
{
    use Auditable;

    protected $fillable = [
        'nombre',
        'codigo_barras',
        'descripcion',
        'precio_compra',
        'unidad_medida',
        'stock',
        'stock_minimo',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
    ];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'producto_ingrediente')
            ->withPivot('cantidad');
    }
}