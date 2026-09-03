<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Promocion;
use App\Models\PromocionUso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    private function tenant(): int
    {
        return Auth::user()->business_instance_id;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Promocion::where('tenant_id', $this->tenant())
            ->with('aplicaA');

        if ($request->filled('codigo')) {
            $query->porCodigo($request->codigo);
        }

        $query->activas();

        $promociones = $query->latest('created_at')->paginate(20);

        return $this->success($promociones);
    }

    public function validar(Request $request): JsonResponse
    {
        $data = $request->validate([
            'codigo' => 'required|string',
            'cart_id' => 'required|exists:carts,id',
            'subtotal' => 'required|numeric|min:0',
            'aplica_item_id' => 'nullable|integer',
        ]);

        $promocion = Promocion::where('tenant_id', $this->tenant())
            ->porCodigo($data['codigo'])
            ->activas()
            ->first();

        if (!$promocion) {
            return $this->error('Promo code is invalid or not available.', 'invalid_code');
        }

        if (!$promocion->estaVigente()) {
            return $this->error('This promotion has expired or is no longer active.', 'expired');
        }

        $descuento = $promocion->calcularDescuento((float) $data['subtotal'], $data['aplica_item_id'] ?? null);

        if ($descuento <= 0) {
            return $this->error('Cart does not meet the promotion criteria.', 'does_not_apply');
        }

        return $this->success([
            'promocion' => [
                'id' => $promocion->id,
                'codigo' => $promocion->codigo,
                'nombre' => $promocion->nombre,
                'tipo' => $promocion->tipo,
                'valor' => $promocion->valor,
                'descuento_aplicable' => $descuento,
            ],
            'aplica' => true,
            'mensaje' => 'Promotion applied successfully.',
        ]);
    }

    public function aplicar(Request $request, string $cartId): JsonResponse
    {
        $data = $request->validate([
            'codigo' => 'required|string',
        ]);

        return DB::transaction(function () use ($data, $cartId) {
            $cart = Cart::where('id', $cartId)
                ->where('tenant_id', $this->tenant())
                ->where('estado', 'active')
                ->firstOrFail();

            $promocion = Promocion::where('tenant_id', $this->tenant())
                ->porCodigo($data['codigo'])
                ->activas()
                ->first();

            if (!$promocion || !$promocion->estaVigente()) {
                return $this->error('Invalid or expired promo code.', 'invalid_code');
            }

            $subtotal = $cart->subtotalItems();

            if ($subtotal < $promocion->minimo_compra) {
                return $this->error('Minimum purchase amount not met. Minimum: ' . $promocion->minimo_compra, 'min_purchase_not_met');
            }

            $descuento = $promocion->calcularDescuento($subtotal);

            if ($descuento <= 0) {
                return $this->error('Cart does not qualify for this promotion.', 'does_not_apply');
            }

            $promocion->increment('uso_actual');
            $cart->applyPromo($descuento, $promocion->tipo);

            PromocionUso::create([
                'promocion_id' => $promocion->id,
                'cart_id' => $cart->id,
                'descuento_aplicado' => $descuento,
            ]);

            return $this->success([
                'cart' => $cart->load('items.producto'),
                'applied_promo' => [
                    'codigo' => $promocion->codigo,
                    'descuento' => $descuento,
                ],
            ], 'Promotion applied successfully.');
        });
    }

    public function eliminar(Request $request, string $cartId): JsonResponse
    {
        $cart = Cart::where('id', $cartId)
            ->where('tenant_id', $this->tenant())
            ->firstOrFail();

        PromocionUso::where('cart_id', $cart->id)->each(function ($uso) {
            Promocion::where('id', $uso->promocion_id)->decrement('uso_actual');
            $uso->delete();
        });

        $cart->update(['descuento' => 0]);
        $cart->recalculateTotals();

        return $this->success($cart->fresh('items.producto'), 'Promotion removed.');
    }

    private function success($data, ?string $message = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'errors' => [],
            'message' => $message ?? 'Success',
        ], $status);
    }

    private function error(string $message, string $code = 'error', $target = null, int $status = 400): JsonResponse
    {
        return response()->json([
            'data' => null,
            'errors' => [[
                'message' => $message,
                'code' => $code,
                'target' => $target,
            ]],
        ], $status);
    }
}
