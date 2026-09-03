<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function listar(int $tenantId, array $filtros = [])
    {
        $query = Cart::with('items.producto')
            ->where('tenant_id', $tenantId);

        if (!empty($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }

        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['session_id'])) {
            $query->where('session_id', $filtros['session_id']);
        }

        return $query->latest('updated_at')->paginate(20);
    }

    public function crear(int $tenantId, array $data): Cart
    {
        return DB::transaction(function () use ($tenantId, $data) {
            $cart = Cart::create([
                'tenant_id' => $tenantId,
                'cliente_id' => $data['cliente_id'] ?? null,
                'type' => $data['type'] ?? 'REC',
                'order_type' => $data['order_type'] ?? 'pickup',
                'email' => $data['email'] ?? null,
                'notas' => $data['notas'] ?? null,
                'estado' => 'active',
            ]);

            if (!empty($data['items'])) {
                foreach ($data['items'] as $itemData) {
                    $producto = Producto::where('id', $itemData['producto_id'])
                        ->where('tenant_id', $tenantId)
                        ->firstOrFail();

                    $cart->addItem($producto, $itemData['cantidad'] ?? 1);
                }
            }

            $cart->recalculateTotals();

            return $cart->load('items.producto');
        });
    }

    public function obtener(int $tenantId, int $cartId): ?Cart
    {
        return Cart::with('items.producto')
            ->where('id', $cartId)
            ->where('tenant_id', $tenantId)
            ->first();
    }

    public function agregarItem(Cart $cart, int $tenantId, int $productoId, int $cantidad, array $extras = []): array
    {
        $producto = Producto::where('id', $productoId)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        if ($producto->tipo_servicio === 'producto' && $producto->stock < $cantidad) {
            return [
                'exito' => false,
                'error' => 'Stock insuficiente para "' . $producto->nombre . '". Disponible: ' . $producto->stock,
                'code' => 'insufficient_stock',
            ];
        }

        $cart->addItem($producto, $cantidad);
        $cart->recalculateTotals();

        return [
            'exito' => true,
            'cart' => $cart->fresh('items.producto'),
        ];
    }

    public function actualizarItem(Cart $cart, int $itemId, array $data): Cart
    {
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        if (isset($data['cantidad'])) {
            $item->cantidad = $data['cantidad'];
        }

        if (isset($data['precio_unitario'])) {
            $item->precio_unitario = $data['precio_unitario'];
        }

        if (isset($data['descuento'])) {
            $item->descuento = $data['descuento'];
        }

        if (isset($data['sin_itbis'])) {
            $item->sin_itbis = $data['sin_itbis'];
        }

        $item->subtotal = ($item->precio_unitario - $item->descuento) * $item->cantidad;
        $item->save();

        $cart->recalculateTotals();

        return $cart->fresh('items.producto');
    }

    public function eliminarItem(Cart $cart, int $itemId): Cart
    {
        $cart->removeItem($itemId);
        $cart->recalculateTotals();

        return $cart->fresh('items.producto');
    }

    public function vaciar(Cart $cart): Cart
    {
        $cart->items()->delete();
        $cart->update(['subtotal' => 0, 'impuestos' => 0, 'descuento' => 0, 'total' => 0]);

        return $cart->fresh('items.producto');
    }
}
