<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Cliente;
use App\Services\SaleCreateService;
use App\Services\PosService;
use App\Models\SesionCaja;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EcommCheckoutController extends Controller
{
    private function tenant(): int
    {
        return Auth::user()->business_instance_id;
    }

    public function submit(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'order_type' => ['sometimes', Rule::in(['pickup', 'delivery'])],
            'customer' => 'sometimes|array',
            'customer.id' => 'nullable|exists:clientes,id',
            'customer.email' => 'nullable|email',
            'customer.phone' => 'nullable|string',
            'customer.name' => 'nullable|string|max:255',
            'customer.address' => 'nullable|string',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'delivery_company_id' => 'nullable|exists:delivery_companies,id',
            'payment_method' => 'sometimes|in:efectivo,tarjeta,transferencia,mixto,fiado',
            'notas' => 'nullable|string|max:500',
            'redirect_url' => 'nullable|url',
        ]);

        return DB::transaction(function () use ($data) {
            // 1. Obtener carrito
            $cart = Cart::with('items.producto')
                ->where('id', $data['cart_id'])
                ->where('tenant_id', $this->tenant())
                ->where('estado', 'active')
                ->firstOrFail();

            // Validar stock
            $items = $cart->items;
            foreach ($items as $item) {
                if ($item->producto->tipo_servicio === 'producto' && $item->producto->stock < $item->cantidad) {
                    return $this->error(
                        'Insufficient stock for "' . $item->producto->nombre . '". Available: ' . $item->producto->stock,
                        'insufficient_stock',
                        ['producto_id' => $item->producto_id, 'available' => $item->producto->stock]
                    );
                }
            }

            // 2. Resolver/crear cliente
            $clienteId = $data['customer']['id'] ?? null;
            if (!$clienteId) {
                $email = $data['customer']['email'] ?? $cart->email;
                $phone = $data['customer']['phone'] ?? null;
                $name = $data['customer']['name'] ?? null;

                if ($email) {
                    $cliente = Cliente::where('email', $email)
                        ->where('tenant_id', $this->tenant())
                        ->first();

                    if (!$cliente) {
                        $cliente = Cliente::create([
                            'tenant_id' => $this->tenant(),
                            'nombre' => $name ?? explode('@', $email)[0],
                            'email' => $email,
                            'telefono' => $phone,
                            'tipo_cliente' => 'consumo',
                            'activo' => true,
                            'origen_cliente' => 'web',
                        ]);
                    }

                    $clienteId = $cliente->id;
                }
            }

            if (!$clienteId) {
                return $this->error('Customer information required for checkout.', 'missing_customer');
            }

            // 3. Actualizar carrito con cliente y tipo de orden
            $cart->update([
                'cliente_id' => $clienteId,
                'order_type' => $data['order_type'] ?? $cart->order_type,
                'estado' => 'checked-out',
            ]);

            // 4. Crear venta usando SaleCreateService
            $sesion = $this->getSessionCaja();

            $itemData = [];
            foreach ($items as $item) {
                $itemData[] = [
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio' => $item->precio_unitario,
                    'subtotal' => $item->subtotal,
                    'descuento' => $item->descuento,
                    'descuento_tipo' => 'monto',
                    'itbis_porcentaje' => $item->itbis_porcentaje,
                    'sin_itbis' => $item->sin_itbis,
                ];
            }

            $metodoPago = $data['payment_method'] ?? 'efectivo';
            $estado = match ($metodoPago) {
                'fiado' => 'pendiente',
                'cuenta_abierta' => 'cuenta_abierta',
                default => 'completada',
            };

            $ventaData = array_merge($data, [
                'producto_id' => array_column($itemData, 'producto_id'),
                'cantidad' => array_column($itemData, 'cantidad'),
                'precio' => array_column($itemData, 'precio'),
                'descuento' => array_column($itemData, 'descuento'),
                'descuento_tipo' => array_column($itemData, 'descuento_tipo'),
                'itbis_porcentaje' => array_column($itemData, 'itbis_porcentaje'),
                'sin_itbis' => array_column($itemData, 'sin_itbis'),
                'subtotal_final' => $cart->subtotal,
                'metodo_pago' => $metodoPago,
                'cliente_id' => $clienteId,
                'estado' => $estado,
                'notas' => ($data['notas'] ?? '') . ' (Checkout e-commerce, cart #' . $cart->id . ')',
                'order_type' => $data['order_type'] ?? 'pickup',
                'delivery_zone_id' => $data['delivery_zone_id'] ?? null,
                'delivery_company_id' => $data['delivery_company_id'] ?? null,
            ]);

            $saleService = app(SaleCreateService::class);
            $venta = $saleService->createSale($ventaData, $sesion);

            // 5. Registrar pagos
            if (in_array($metodoPago, ['efectivo', 'tarjeta', 'transferencia'])) {
                \App\Models\Pago::create([
                    'tenant_id' => $this->tenant(),
                    'venta_id' => $venta->id,
                    'caja_id' => $sesion->caja_id,
                    'sesion_caja_id' => $sesion->id,
                    'monto' => (float) $venta->total,
                    'metodo_pago' => $metodoPago,
                    'nota' => 'Pago e-commerce cart #' . $cart->id,
                    'fecha_pago' => now(),
                ]);
            }

            // 6. Generar NCF si aplica
            if ($venta->tipo_comprobante === 'ncf' && empty($venta->ncf)) {
                $venta->update([
                    'ncf_tipo' => 'B01',
                    'ncf' => $this->generateNCF(),
                ]);
            }

            return $this->success([
                'venta' => $venta->load(['cliente', 'detalles']),
                'checkout_url' => $data['redirect_url'] ?? null,
            ], 'Order created successfully.');
        });
    }

    public function submitGuest(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'delivery_zone_id' => 'nullable|exists:delivery_zones,id',
            'payment_method' => 'sometimes|in:efectivo,tarjeta,transferencia,mixto',
            'redirect_url' => 'nullable|url',
        ]);

        return DB::transaction(function () use ($data) {
            // Similar a submit() pero sin buscar cliente existente
            // Crear cliente walk-in

            $cart = Cart::where('id', $data['cart_id'])
                ->where('tenant_id', $this->tenant())
                ->where('estado', 'active')
                ->firstOrFail();

            // Crear cliente walk-in
            $cliente = Cliente::create([
                'tenant_id' => $this->tenant(),
                'nombre' => $data['customer_name'],
                'email' => $data['customer_email'],
                'telefono' => $data['customer_phone'],
                'direccion' => $data['customer_address'] ?? null,
                'tipo_cliente' => 'consumo',
                'origen_cliente' => 'web',
                'activo' => true,
            ]);

            // Continuar con el mismo flujo que submit()...
            // (reutilizar la lógica de submitGuest)

            $cart->update([
                'cliente_id' => $cliente->id,
                'email' => $data['customer_email'],
                'estado' => 'checked-out',
            ]);

            return $this->success(['cliente_id' => $cliente->id], 'Guest customer created.');
        });
    }

    private function getSessionCaja(): SesionCaja
    {
        $sesion = SesionCaja::where('estado', 'abierta')
            ->where('user_id', Auth::id())
            ->first();

        if (!$sesion) {
            throw new \Exception('No active cash register session. Open a caja first.');
        }

        return $sesion;
    }

    private function generateNCF(): string
    {
        $year = date('Y');
        $prefix = 'B01';
        $seq = \App\Models\NcfSequence::where('ncf_tipo', 'B01')
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->first();

        if (!$seq) {
            $seq = \App\Models\NcfSequence::create([
                'ncf_tipo' => 'B01',
                'seguiente' => 1,
                'anio' => $year,
            ]);
        }

        $ncf = sprintf('%s-%09d', $prefix, $seq->seguiente);
        $seq->increment('seguiente');

        return $ncf;
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
