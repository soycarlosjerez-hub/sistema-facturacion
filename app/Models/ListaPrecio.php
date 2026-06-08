<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListaPrecio extends Model
{
    protected $table = 'lista_precios';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'vigencia_desde', 'vigencia_hasta', 'activa',
    ];

    protected $casts = [
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
        'activa' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ListaPrecioItem::class);
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('activa', true)
            ->where(fn($q) => $q->whereNull('vigencia_desde')->orWhere('vigencia_desde', '<=', now()))
            ->where(fn($q) => $q->whereNull('vigencia_hasta')->orWhere('vigencia_hasta', '>=', now()));
    }

    public function getPrecioProducto(Producto $producto): ?float
    {
        $item = $this->items()->where('producto_id', $producto->id)->first();
        return $item ? (float) $item->precio : null;
    }
}
