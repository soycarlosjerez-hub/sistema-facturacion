<?php

namespace App\Services;

use App\Models\Promocion;
use App\Models\PromocionUso;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class PromocionService
{
    public function listarActivas(int $tenantId)
    {
        return Promocion::where('tenant_id', $tenantId)
            ->activas()
            ->latest()
            ->get();
    }

    public function buscarPorCodigo(int $tenantId, string $codigo): ?Promocion
    {
        return Promocion::where('tenant_id', $tenantId)
            ->porCodigo($codigo)
            ->activas()
            ->first();
    }

    public function validar(int $tenantId, string $codigo, float $subtotal, ?int $aplicaItemId = null): array
    {
        $promocion = $this->buscarPorCodigo($tenantId, $codigo);

        if (!$promocion) {
            return ['valida' => false, 'error' => 'Código de promoción inválido o no disponible.', 'code' => 'invalid_code'];
        }

        if (!$promocion->estaVigente()) {
            return ['valida' => false, 'error' => 'Esta promoción ha expirado o ya no está activa.', 'code' => 'expired'];
        }

        $descuento = $promocion->calcularDescuento($subtotal, $aplicaItemId);

        if ($descuento <= 0) {
            return ['valida' => false, 'error' => 'El carrito no cumple con los criterios de la promoción.', 'code' => 'does_not_apply'];
        }

        return [
            'valida' => true,
            'promocion' => $promocion,
            'descuento' => $descuento,
        ];
    }

    public function aplicarAlCarrito(Cart $cart, int $tenantId, string $codigo): array
    {
        return DB::transaction(function () use ($cart, $tenantId, $codigo) {
            $validacion = $this->validar($tenantId, $codigo, $cart->subtotalItems());

            if (!$validacion['valida']) {
                return $validacion;
            }

            $promocion = $validacion['promocion'];
            $descuento = $validacion['descuento'];

            if ($cart->subtotalItems() < $promocion->minimo_compra) {
                return [
                    'valida' => false,
                    'error' => 'Monto mínimo de compra no alcanzado. Mínimo: ' . $promocion->minimo_compra,
                    'code' => 'min_purchase_not_met',
                ];
            }

            $promocion->increment('uso_actual');
            $cart->applyPromo($descuento, $promocion->tipo);

            PromocionUso::create([
                'promocion_id' => $promocion->id,
                'cart_id' => $cart->id,
                'descuento_aplicado' => $descuento,
            ]);

            return [
                'valida' => true,
                'cart' => $cart->fresh('items.producto'),
                'applied_promo' => [
                    'codigo' => $promocion->codigo,
                    'descuento' => $descuento,
                ],
            ];
        });
    }

    public function eliminarDeCarrito(Cart $cart): void
    {
        PromocionUso::where('cart_id', $cart->id)->each(function ($uso) {
            Promocion::where('id', $uso->promocion_id)->decrement('uso_actual');
            $uso->delete();
        });

        $cart->update(['descuento' => 0]);
        $cart->recalculateTotals();
    }
}
