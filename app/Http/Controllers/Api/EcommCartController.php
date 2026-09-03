<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EcommCartController extends Controller
{
    private function tenant(): int
    {
        return Auth::user()->business_instance_id;
    }

    public function index(Request $request): JsonResponse
    {
        $query = Cart::with('items.producto')
            ->where('tenant_id', $this->tenant());

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        $carts = $query->latest('updated_at')->paginate(20);

        return $this->success($carts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id' => 'nullable|exists:clientes,id',
            'type' => 'sometimes|in:REC,MED',
            'order_type' => 'sometimes|in:pickup,delivery',
            'email' => 'nullable|email',
            'notas' => 'nullable|string|max:500',
            'items' => 'nullable|array',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $cart = Cart::create([
                'tenant_id' => $this->tenant(),
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
                        ->where('tenant_id', $this->tenant())
                        ->firstOrFail();

                    $cart->addItem($producto, $itemData['cantidad'] ?? 1);
                }
            }

            $cart->recalculateTotals();
            $cart->load('items.producto');

            return $this->success($cart, 'Cart created successfully.', 201);
        });
    }

    public function show($id): JsonResponse
    {
        $cart = Cart::with('items.producto')
            ->where('id', $id)
            ->where('tenant_id', $this->tenant())
            ->firstOrFail();

        return $this->success($cart);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $cart = Cart::where('id', $id)
            ->where('tenant_id', $this->tenant())
            ->firstOrFail();

        $data = $request->validate([
            'type' => 'sometimes|in:REC,MED',
            'order_type' => 'sometimes|in:pickup,delivery',
            'email' => 'nullable|email',
            'notas' => 'nullable|string|max:500',
            'estado' => 'sometimes|in:active,checked-out',
        ]);

        $cart->update($data);

        return $this->success($cart);
    }

    public function add(\App\Http\Requests\Api\ItemRequest $request, $id): JsonResponse
    {
        $cart = Cart::where('id', $id)
            ->where('tenant_id', $this->tenant())
            ->where('estado', 'active')
            ->firstOrFail();

        return DB::transaction(function () use ($cart, $request) {
            $producto = Producto::where('id', $request->producto_id)
                ->where('tenant_id', $this->tenant())
                ->firstOrFail();

            if ($producto->tipo_servicio === 'producto' && $producto->stock < $request->cantidad) {
                return $this->error('Insufficient stock for product "' . $producto->nombre . '". Available: ' . $producto->stock, 'insufficient_stock', ['available' => $producto->stock], 400);
            }

            $cart->addItem($producto, $request->cantidad);
            $cart->recalculateTotals();

            $cart->load('items.producto');

            return $this->success($cart, 'Item added to cart.');
        });
    }

    public function updateItem($cartId, $itemId, Request $request): JsonResponse
    {
        $cart = Cart::where('id', $cartId)
            ->where('tenant_id', $this->tenant())
            ->where('estado', 'active')
            ->firstOrFail();

        $data = $request->validate([
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'sin_itbis' => 'nullable|boolean',
        ]);

        return DB::transaction(function () use ($cart, $itemId, $data) {
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

            $cart->load('items.producto');

            return $this->success($cart, 'Cart item updated.');
        });
    }

    public function removeItem($cartId, $itemId): JsonResponse
    {
        $cart = Cart::where('id', $cartId)
            ->where('tenant_id', $this->tenant())
            ->where('estado', 'active')
            ->firstOrFail();

        $cart->removeItem($itemId);
        $cart->recalculateTotals();

        return $this->success($cart, 'Item removed from cart.');
    }

    public function clear($id): JsonResponse
    {
        $cart = Cart::where('id', $id)
            ->where('tenant_id', $this->tenant())
            ->firstOrFail();

        $cart->items()->delete();
        $cart->update(['subtotal' => 0, 'impuestos' => 0, 'descuento' => 0, 'total' => 0]);

        return $this->success($cart, 'Cart cleared.');
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
