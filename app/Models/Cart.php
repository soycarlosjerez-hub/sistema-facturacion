<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\TenantScope;

class Cart extends Model
{
    use TenantScope;

    protected $table = 'carts';

    protected $fillable = [
        'tenant_id',
        'cliente_id',
        'type',
        'order_type',
        'estado',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'session_id',
        'email',
        'notas',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('estado', 'active');
    }

    public function scopeConCliente($query)
    {
        return $query->whereNotNull('cliente_id');
    }

    public function scopePorSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function subtotalItems(): float
    {
        return $this->items->sum(function ($item) {
            return ($item->precio_unitario - $item->descuento) * $item->cantidad;
        });
    }

    public function totalItems(): int
    {
        return $this->items->sum('cantidad');
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function addItem(Producto $producto, int $cantidad = 1): CartItem
    {
        $precioUnitario = $producto->precio;

        $existingItem = $this->items()->where('producto_id', $producto->id)->first();

        if ($existingItem) {
            $existingItem->cantidad += $cantidad;
            $existingItem->subtotal = $existingItem->precio_unitario * $existingItem->cantidad;
            $existingItem->save();
            return $existingItem;
        }

        return $this->items()->create([
            'producto_id' => $producto->id,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $precioUnitario * $cantidad,
            'descuento' => 0,
            'itbis_porcentaje' => $producto->itbis_porcentaje,
            'sin_itbis' => false,
        ]);
    }

    public function updateItemQuantity(int $cartItemId, int $cantidad): ?CartItem
    {
        if ($cantidad <= 0) {
            return $this->removeItem($cartItemId);
        }

        $item = $this->items()->find($cartItemId);
        if (!$item) {
            return null;
        }

        $item->cantidad = $cantidad;
        $item->subtotal = $item->precio_unitario * $cantidad;
        $item->save();

        return $item;
    }

    public function removeItem(int $cartItemId): bool
    {
        return $this->items()->where('id', $cartItemId)->delete() > 0;
    }

    public function recalculateTotals(): void
    {
        $subtotal = 0;
        $impuestos = 0;

        foreach ($this->items as $item) {
            $lineaSubtotal = ($item->precio_unitario - $item->descuento) * $item->cantidad;
            $subtotal += $lineaSubtotal;

            if (!$item->sin_itbis) {
                $impuestos += $lineaSubtotal * ($item->itbis_porcentaje / 100);
            }
        }

        $this->subtotal = round($subtotal, 2);
        $this->impuestos = round($impuestos, 2);
        $this->total = round($subtotal + $impuestos - $this->descuento, 2);
        $this->save();
    }

    public function applyPromo(float $descuento, string $tipo = 'monto'): void
    {
        $this->descuento = round($descuento, 2);
        $this->recalculateTotals();
    }

    public function applyLoyaltyPoints(int $puntos, float $tasaCambio = 1): void
    {
        $descuento = $puntos * $tasaCambio;
        $this->applyPromo($descuento, 'monto');
    }
}
