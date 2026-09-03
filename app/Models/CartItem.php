<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $table = 'cart_items';

    protected $fillable = [
        'cart_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'descuento',
        'itbis_porcentaje',
        'sin_itbis',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbis_porcentaje' => 'decimal:2',
        'sin_itbis' => 'boolean',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function checkStock(): bool
    {
        if ($this->producto->tipo_servicio === 'servicio' || $this->producto->tipo_servicio === 'general') {
            return true;
        }

        return $this->producto->stock >= $this->cantidad;
    }

    public function updateQuantity(int $cantidad): bool
    {
        if ($cantidad <= 0) {
            return $this->delete();
        }

        if (!$this->checkStock()) {
            throw new \Exception('Stock insuficiente para este producto.');
        }

        $this->cantidad = $cantidad;
        $this->subtotal = $this->precio_unitario * $cantidad;
        return $this->save();
    }

    public function updatePrice(float $precio): bool
    {
        $this->precio_unitario = $precio;
        $this->subtotal = $this->precio_unitario * $this->cantidad;
        return $this->save();
    }

    public function applyDiscount(float $monto, string $tipo = 'monto'): void
    {
        if ($tipo === 'porcentaje') {
            $this->descuento = $this->subtotal * ($monto / 100);
        } else {
            $this->descuento = min($monto, $this->subtotal);
        }

        $this->subtotal = ($this->precio_unitario - $this->descuento) * $this->cantidad;
    }
}
