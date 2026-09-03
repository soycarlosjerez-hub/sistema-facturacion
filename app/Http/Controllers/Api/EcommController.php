<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Promocion;
use App\Models\PromocionUso;
use App\Models\LealtadCuenta;
use App\Models\LealtadMovimiento;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Pago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Flowhub v3 compatible Ecomm API adapter.
 *
 * Maps Flowhub UUID-based endpoints to ERP BIGINT entities.
 * Money amounts are in cents (Flowhub convention).
 * Auth via clientId + key headers.
 */
class EcommController extends Controller
{
    private int $tenantId;

    public function __construct()
    {
        $this->tenantId = Auth::user()->business_instance_id ?? 0;
    }

    // ─── Helpers ───────────────────────────────────────────────

    private function flowId(int $id): string
    {
        $model = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? 'unknown';
        $modelMap = [
            'products' => 'Producto', 'productShow' => 'Producto',
            'customers' => 'Cliente', 'customerCreate' => 'Cliente',
            'cartCreate' => 'Cart', 'cartShow' => 'Cart', 'cartAddItem' => 'Cart',
            'cartUpdateItem' => 'Cart', 'cartRemoveItem' => 'Cart',
            'formatCart' => 'Cart',
            'deals' => 'Promocion', 'dealApply' => 'Promocion',
            'checkout' => 'Venta', 'rewardRedeem' => 'LealtadCuenta',
        ];
        $modelName = $modelMap[$model] ?? 'Producto';

        $existing = DB::table('flowhub_id_map')
            ->where('real_id', $id)
            ->where('model', $modelName)
            ->where('tenant_id', $this->tenantId)
            ->value('flow_id');

        if ($existing) {
            return $existing;
        }

        $uuid = Str::uuid()->toString();
        $this->storeMap($uuid, $modelName, $id);
        return $uuid;
    }

    private function mapId(string $flowId, string $model): ?int
    {
        $result = DB::table('flowhub_id_map')
            ->where('flow_id', $flowId)
            ->where('model', $model)
            ->where('tenant_id', $this->tenantId)
            ->value('real_id');

        if ($result) {
            return (int) $result;
        }

        if (is_numeric($flowId)) {
            return (int) $flowId;
        }

        return null;
    }

    private function storeMap(string $flowId, string $model, int $realId): void
    {
        DB::table('flowhub_id_map')->updateOrInsert(
            ['flow_id' => $flowId, 'model' => $model],
            ['real_id' => $realId, 'tenant_id' => $this->tenantId]
        );
    }

    private function centsToDecimal(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    private function decimalToCents(string $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function response($data, string $message = 'Success', int $status = 200): JsonResponse
    {
        return response()->json([
            'status' => $status >= 400 ? 'error' : 'success',
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    private function error(string $message, int $status = 400): JsonResponse
    {
        return $this->response(null, $message, $status);
    }

    // ─── Products ──────────────────────────────────────────────

    public function products(Request $request): JsonResponse
    {
        $query = Producto::where('tenant_id', $this->tenantId)
            ->where('activo', true)
            ->select('id', 'nombre', 'descripcion', 'precio', 'stock', 'codigo_barras',
                     'marca', 'modelo', 'imagen', 'itbis_porcentaje', 'unidad_medida');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo_barras', 'like', "%{$term}%")
                  ->orWhere('marca', 'like', "%{$term}%")
                  ->orWhere('modelo', 'like', "%{$term}%");
            });
        }

        $page = $request->get('page', 1);
        $perPage = min($request->get('limit', 25), 100);

        $products = $query->orderBy('nombre')->paginate($perPage, ['*'], 'page', $page);

        $mapped = $products->getCollection()->map(function ($p) {
            return [
                'id' => $this->flowId($p->id),
                'internal_id' => $p->id,
                'name' => $p->nombre,
                'description' => $p->descripcion,
                'price' => $this->decimalToCents($p->precio),
                'qtyOnHand' => $p->stock,
                'sku' => $p->codigo_barras,
                'brand' => $p->marca,
                'model' => $p->modelo,
                'imageUrl' => $p->imagen ? asset('storage/' . $p->imagen) : null,
                'taxRate' => (float) $p->itbis_porcentaje,
                'unitOfMeasure' => $p->unidad_medida ?? 'Unidad',
            ];
        });

        return $this->response([
            'products' => $mapped,
            'pagination' => [
                'total' => $products->total(),
                'page' => $products->currentPage(),
                'limit' => $products->perPage(),
                'totalPages' => $products->lastPage(),
            ],
        ]);
    }

    public function productShow(string $id): JsonResponse
    {
        $realId = $this->mapId($id, 'Producto') ?? $id;
        $p = Producto::where('id', $realId)
            ->where('tenant_id', $this->tenantId)
            ->firstOrFail();

        return $this->response([
            'id' => $this->flowId($p->id),
            'internal_id' => $p->id,
            'name' => $p->nombre,
            'description' => $p->descripcion,
            'price' => $this->decimalToCents($p->precio),
            'costPrice' => $this->decimalToCents($p->precio_compra ?? 0),
            'qtyOnHand' => $p->stock,
            'qtyMinimum' => $p->stock_minimo,
            'sku' => $p->codigo_barras,
            'brand' => $p->marca,
            'model' => $p->modelo,
            'imageUrl' => $p->imagen ? asset('storage/' . $p->imagen) : null,
            'taxRate' => (float) $p->itbis_porcentaje,
            'unitOfMeasure' => $p->unidad_medida ?? 'Unidad',
            'active' => $p->activo,
        ]);
    }

    // ─── Customers (Clientes) ─────────────────────────────────

    public function customers(Request $request): JsonResponse
    {
        $query = Cliente::where('tenant_id', $this->tenantId);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('telefono', 'like', "%{$term}%")
                  ->orWhere('cedula', 'like', "%{$term}%");
            });
        }

        $page = $request->get('page', 1);
        $perPage = min($request->get('limit', 25), 100);
        $customers = $query->orderBy('nombre')->paginate($perPage, ['*'], 'page', $page);

        $mapped = $customers->getCollection()->map(function ($c) {
            return [
                'id' => $this->flowId($c->id),
                'internal_id' => $c->id,
                'firstName' => $c->nombre,
                'lastName' => $c->apellido ?? '',
                'email' => $c->email,
                'phone' => $c->telefono,
                'identification' => $c->cedula,
            ];
        });

        return $this->response([
            'customers' => $mapped,
            'pagination' => [
                'total' => $customers->total(),
                'page' => $customers->currentPage(),
                'limit' => $customers->perPage(),
                'totalPages' => $customers->lastPage(),
            ],
        ]);
    }

    public function customerCreate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'identification' => 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create([
            'tenant_id' => $this->tenantId,
            'nombre' => $data['firstName'],
            'apellido' => $data['lastName'] ?? '',
            'email' => $data['email'] ?? null,
            'telefono' => $data['phone'] ?? null,
            'cedula' => $data['identification'] ?? null,
        ]);

        return $this->response([
            'id' => $this->flowId($cliente->id),
            'internal_id' => $cliente->id,
            'firstName' => $cliente->nombre,
            'lastName' => $cliente->apellido,
            'email' => $cliente->email,
            'phone' => $cliente->telefono,
        ], 'Customer created.', 201);
    }

    // ─── Carts ─────────────────────────────────────────────────

    public function cartCreate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customerFlowId' => 'nullable|string',
            'sessionId' => 'nullable|string|max:255',
            'orderType' => 'nullable|string|in:pos,web,phone',
        ]);

        $clienteId = null;
        if (!empty($data['customerFlowId'])) {
            $clienteId = $this->mapId($data['customerFlowId'], 'Cliente');
        }

        $cart = Cart::create([
            'tenant_id' => $this->tenantId,
            'cliente_id' => $clienteId,
            'session_id' => $data['sessionId'] ?? Str::uuid()->toString(),
            'order_type' => $data['orderType'] ?? 'pos',
            'estado' => 'active',
        ]);

        return $this->response([
            'id' => $this->flowId($cart->id),
            'internal_id' => $cart->id,
            'status' => 'active',
        ], 'Cart created.', 201);
    }

    public function cartShow(string $id): JsonResponse
    {
        $realId = $this->mapId($id, 'Cart') ?? $id;
        $cart = Cart::with('items.producto')
            ->where('id', $realId)
            ->where('tenant_id', $this->tenantId)
            ->firstOrFail();

        return $this->response($this->formatCart($cart));
    }

    public function cartAddItem(Request $request, string $cartId): JsonResponse
    {
        $data = $request->validate([
            'productId' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'priceOverride' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $realCartId = $this->mapId($cartId, 'Cart') ?? $cartId;
        $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : (int) $realCartId;

        $realProductId = $this->mapId($data['productId'], 'Producto') ?? $data['productId'];
        $realProductIdInt = is_numeric($realProductId) ? (int) $realProductId : (int) $realProductId;

        $cart = Cart::where('id', $realCartIdInt)
            ->where('tenant_id', $this->tenantId)
            ->where('estado', 'active')
            ->firstOrFail();

        $producto = Producto::where('id', $realProductIdInt)
            ->where('tenant_id', $this->tenantId)
            ->firstOrFail();

        $precio = $data['priceOverride']
            ? $this->centsToDecimal($data['priceOverride'])
            : $producto->precio;

        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'producto_id' => $producto->id,
            'cantidad' => $data['quantity'],
            'precio_unitario' => $precio,
            'subtotal' => round((float) $precio * $data['quantity'], 2),
            'itbis_porcentaje' => $producto->itbis_porcentaje,
            'notas' => $data['notes'] ?? null,
        ]);

        $cart->recalculateTotals();

        return $this->response($this->formatCart($cart->fresh('items.producto')), 'Item added.', 201);
    }

    public function cartUpdateItem(Request $request, string $cartId, string $itemId): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $realCartId = $this->mapId($cartId, 'Cart') ?? $cartId;
        $realItemId = $this->mapId($itemId, 'CartItem') ?? $itemId;

        $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : (int) $realCartId;
        $realItemIdInt = is_numeric($realItemId) ? (int) $realItemId : (int) $realItemId;

        $cartItem = CartItem::where('id', $realItemIdInt)
            ->where('cart_id', $realCartIdInt)
            ->firstOrFail();

        $cartItem->update([
            'cantidad' => $data['quantity'],
            'subtotal' => round((float) $cartItem->precio_unitario * $data['quantity'], 2),
        ]);

        $cart = Cart::with('items.producto')->find($realCartIdInt);
        if ($cart) {
            $cart->recalculateTotals();
        }

        return $this->response($this->formatCart($cart));
    }

    public function cartRemoveItem(string $cartId, string $itemId): JsonResponse
    {
        $realCartId = $this->mapId($cartId, 'Cart') ?? $cartId;
        $realItemId = $this->mapId($itemId, 'CartItem') ?? $itemId;

        $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : (int) $realCartId;
        $realItemIdInt = is_numeric($realItemId) ? (int) $realItemId : (int) $realItemId;

        CartItem::where('id', $realItemIdInt)
            ->where('cart_id', $realCartIdInt)
            ->delete();

        $cart = Cart::with('items.producto')->find($realCartIdInt);
        if ($cart) {
            $cart->recalculateTotals();
        }

        return $this->response($this->formatCart($cart));
    }

    // ─── Checkout ──────────────────────────────────────────────

    public function checkout(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cartId' => 'required|string',
            'paymentMethod' => 'required|string|in:efectivo,tarjeta,transferencia,mixto,fiado',
            'amountPaid' => 'nullable|integer|min:0',
            'reference' => 'nullable|string|max:255',
            'customerFlowId' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $realCartId = $this->mapId($data['cartId'], 'Cart') ?? $data['cartId'];
        $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : (int) $realCartId;

        $cart = Cart::with('items.producto')
            ->where('id', $realCartIdInt)
            ->where('tenant_id', $this->tenantId)
            ->where('estado', 'active')
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return $this->error('Cart is empty.', 422);
        }

        // Resolve customer
        $clienteId = $cart->cliente_id;
        if (!empty($data['customerFlowId'])) {
            $clienteId = $this->mapId($data['customerFlowId'], 'Cliente') ?? $clienteId;
        }

        if (!$clienteId) {
            $cliente = Cliente::create([
                'tenant_id' => $this->tenantId,
                'nombre' => 'Cliente Walk-in',
                'email' => $data['email'] ?? null,
            ]);
            $clienteId = $cliente->id;
        }

        return DB::transaction(function () use ($cart, $clienteId, $data) {
            // Create Venta
            $total = (float) $cart->total;
            $impuestos = (float) $cart->impuestos;
            $subtotal = (float) $cart->subtotal;
            $descuento = (float) $cart->descuento;

            $venta = Venta::create([
                'tenant_id' => $this->tenantId,
                'cliente_id' => $clienteId,
                'user_id' => Auth::id(),
                'fecha' => now()->toDateTimeString(),
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'descuento' => $descuento,
                'total' => $total,
                'estado' => 'completada',
                'metodo_pago' => $data['paymentMethod'],
                'tipo_comprobante' => 'ncf',
            ]);

            // Create VentaDetalle
            foreach ($cart->items as $item) {
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'subtotal' => $item->subtotal,
                    'itbis_porcentaje' => $item->itbis_porcentaje,
                    'descuento' => $item->descuento ?? 0,
                ]);

                // Decrement stock
                if ($item->producto && $item->producto->tipo_servicio === 'producto') {
                    $item->producto->decrement('stock', $item->cantidad);
                }

                // Increment ventas_count
                Producto::where('id', $item->producto_id)->increment('ventas_count', $item->cantidad);
            }

            // Create payment
            $montoPagado = $data['amountPaid'] ? $this->centsToDecimal($data['amountPaid']) : $total;
            Pago::create([
                'tenant_id' => $this->tenantId,
                'venta_id' => $venta->id,
                'monto' => $montoPagado,
                'metodo_pago' => $data['paymentMethod'],
                'nota' => $data['reference'] ?? null,
                'fecha_pago' => now()->toDateTimeString(),
            ]);

            // Mark cart as completed
            $cart->update(['estado' => 'completed']);

            // Register promo usage if applied
            if ($cart->descuento > 0) {
                $promoUsos = PromocionUso::where('cart_id', $cart->id)->get();
                foreach ($promoUsos as $pu) {
                    $pu->update(['venta_id' => $venta->id]);
                }
            }

            // Register loyalty points
            if ($clienteId) {
                $puntosGanados = (int) floor($total);
                if ($puntosGanados > 0) {
                    $cuenta = LealtadCuenta::firstOrCreate(
                        ['tenant_id' => $this->tenantId, 'cliente_id' => $clienteId],
                        ['nivel' => 'bronce', 'tasa_cambio' => 1]
                    );
                    $cuenta->ganarPuntos($puntosGanados);

                    LealtadMovimiento::create([
                        'cuenta_id' => $cuenta->id,
                        'tipo' => 'ganar',
                        'cantidad' => $puntosGanados,
                        'venta_id' => $venta->id,
                        'notas' => "Puntos ganados en venta #{$venta->id}",
                    ]);
                }
            }

            return $this->response([
                'orderId' => $this->flowId($venta->id),
                'internal_id' => $venta->id,
                'total' => $this->decimalToCents($venta->total),
                'paid' => $this->decimalToCents($montoPagado),
                'status' => 'completed',
                'pointsEarned' => $clienteId ? (int) floor($total) : 0,
            ], 'Checkout completed.', 201);
        });
    }

    // ─── Deals (Promociones) ──────────────────────────────────

    public function deals(Request $request): JsonResponse
    {
        $promos = Promocion::where('tenant_id', $this->tenantId)
            ->activas()
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $this->flowId($p->id),
                    'internal_id' => $p->id,
                    'code' => $p->codigo,
                    'name' => $p->nombre,
                    'description' => $p->descripcion,
                    'type' => $p->tipo,
                    'value' => $p->tipo === 'porcentaje' ? $p->valor : $this->decimalToCents($p->valor),
                    'minPurchase' => $this->decimalToCents($p->minimo_compra),
                    'validFrom' => $p->valido_desde?->toDateString(),
                    'validUntil' => $p->valido_hasta?->toDateString(),
                    'active' => $p->activa,
                ];
            });

        return $this->response(['deals' => $promos]);
    }

    public function dealApply(Request $request, string $cartId): JsonResponse
    {
        $data = $request->validate([
            'code' => 'required|string',
        ]);

        $realCartId = $this->mapId($cartId, 'Cart') ?? $cartId;

        $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : $realCartId;
        $cart = Cart::with('items.producto')
            ->where('id', $realCartIdInt)
            ->where('tenant_id', $this->tenantId)
            ->firstOrFail();

        $promocion = Promocion::where('tenant_id', $this->tenantId)
            ->where('codigo', strtoupper($data['code']))
            ->activas()
            ->first();

        if (!$promocion || !$promocion->estaVigente()) {
            return $this->error('Invalid or expired deal code.');
        }

        $subtotal = $cart->subtotalItems();
        $descuento = $promocion->calcularDescuento($subtotal);

        if ($descuento <= 0) {
            return $this->error('Cart does not qualify for this deal.');
        }

        $cart->applyPromo($descuento, $promocion->tipo);
        $promocion->increment('uso_actual');

        PromocionUso::create([
            'promocion_id' => $promocion->id,
            'cart_id' => $cart->id,
            'descuento_aplicado' => $descuento,
        ]);

        return $this->response([
            'deal' => [
                'code' => $promocion->codigo,
                'discount' => $this->decimalToCents($descuento),
            ],
            'cart' => $this->formatCart($cart->fresh('items.producto')),
        ], 'Deal applied.');
    }

    // ─── Rewards (Lealtad) ────────────────────────────────────

    public function rewards(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customerFlowId' => 'required|string',
        ]);

        $realId = $this->mapId($data['customerFlowId'], 'Cliente') ?? $data['customerFlowId'];
        $realIdInt = is_numeric($realId) ? (int) $realId : $realId;

        $cuenta = LealtadCuenta::where('tenant_id', $this->tenantId)
            ->where('cliente_id', $realIdInt)
            ->first();

        if (!$cuenta) {
            return $this->response([
                'points' => 0,
                'tier' => 'bronce',
                'availableRewards' => [],
            ]);
        }

        $puntos = $cuenta->puntosDisponibles();
        $rewards = [
            ['id' => '100', 'name' => '5% Descuento', 'costInPoints' => 100, 'description' => '5% off en tu compra'],
            ['id' => '200', 'name' => '10% Descuento', 'costInPoints' => 200, 'description' => '10% off en tu compra'],
            ['id' => '300', 'name' => '15% Descuento', 'costInPoints' => 300, 'description' => '15% off en tu compra'],
            ['id' => '150', 'name' => 'Envío Gratis', 'costInPoints' => 150, 'description' => 'Envío gratuito'],
        ];

        return $this->response([
            'points' => $puntos,
            'tier' => $cuenta->nivel,
            'totalEarned' => $cuenta->puntos_acumulados,
            'totalRedeemed' => $cuenta->puntos_canjeados,
            'availableRewards' => $rewards,
        ]);
    }

    public function rewardRedeem(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customerFlowId' => 'required|string',
            'rewardId' => 'required|string',
            'cartId' => 'nullable|string',
        ]);

        $realId = $this->mapId($data['customerFlowId'], 'Cliente') ?? $data['customerFlowId'];
        $realIdInt = is_numeric($realId) ? (int) $realId : $realId;

        $cuenta = LealtadCuenta::where('tenant_id', $this->tenantId)
            ->where('cliente_id', $realIdInt)
            ->firstOrFail();

        $rewardId = $data['rewardId'];
        $recompensa = null;
        if ($rewardId === '100') {
            $recompensa = ['costo' => 100, 'descuento' => 0.05, 'nombre' => '5% Descuento'];
        } elseif ($rewardId === '200') {
            $recompensa = ['costo' => 200, 'descuento' => 0.10, 'nombre' => '10% Descuento'];
        } elseif ($rewardId === '300') {
            $recompensa = ['costo' => 300, 'descuento' => 0.15, 'nombre' => '15% Descuento'];
        } elseif ($rewardId === '150') {
            $recompensa = ['costo' => 150, 'descuento' => 'envio_gratis', 'nombre' => 'Envío Gratis'];
        }

        if (!$recompensa) {
            return $this->error('Invalid reward.');
        }

        if (!$cuenta->puedeCanjear($recompensa['costo'])) {
            return $this->error('Insufficient points. Available: ' . $cuenta->puntosDisponibles());
        }

        $cuenta->canjearPuntos($recompensa['costo']);
        LealtadMovimiento::create([
            'cuenta_id' => $cuenta->id,
            'tipo' => 'canjear',
            'cantidad' => $recompensa['costo'],
            'notas' => 'Flowhub reward: ' . $recompensa['nombre'],
        ]);

        // Apply discount to cart if provided
        if (!empty($data['cartId']) && is_numeric($recompensa['descuento'])) {
            $realCartId = $this->mapId($data['cartId'], 'Cart') ?? $data['cartId'];
            $realCartIdInt = is_numeric($realCartId) ? (int) $realCartId : (int) $realCartId;
            $cart = Cart::where('tenant_id', $this->tenantId)->find($realCartIdInt);
            if ($cart) {
                $descuento = $cart->subtotalItems() * $recompensa['descuento'];
                $cart->applyPromo($descuento, 'porcentaje');
            }
        }

        return $this->response([
            'pointsRemaining' => $cuenta->puntosDisponibles(),
            'tier' => $cuenta->nivel,
            'rewardRedeemed' => $recompensa['nombre'],
        ], 'Reward redeemed.');
    }

    // ─── Private Helpers ───────────────────────────────────────

    private function formatCart(Cart $cart): array
    {
        return [
            'id' => $this->flowId($cart->id),
            'internal_id' => $cart->id,
            'status' => $cart->estado,
            'subtotal' => $this->decimalToCents($cart->subtotal),
            'tax' => $this->decimalToCents($cart->impuestos),
            'discount' => $this->decimalToCents($cart->descuento),
            'total' => $this->decimalToCents($cart->total),
            'items' => $cart->items->map(function ($item) {
                return [
                    'id' => $this->flowId($item->id),
                    'internal_id' => $item->id,
                    'productId' => $this->flowId($item->producto_id),
                    'productName' => $item->producto?->nombre,
                    'quantity' => $item->cantidad,
                    'unitPrice' => $this->decimalToCents($item->precio_unitario),
                    'subtotal' => $this->decimalToCents($item->subtotal),
                    'taxRate' => (float) $item->itbis_porcentaje,
                    'notes' => $item->notas,
                ];
            }),
        ];
    }
}
