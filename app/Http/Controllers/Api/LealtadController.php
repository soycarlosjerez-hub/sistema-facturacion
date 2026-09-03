<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LealtadCuenta;
use App\Models\LealtadMovimiento;
use App\Models\Cart;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LealtadController extends Controller
{
    private function tenant(): int
    {
        return Auth::user()->business_instance_id;
    }

    public function get(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cart_id' => 'nullable|exists:carts,id',
        ]);

        $cuenta = LealtadCuenta::where('tenant_id', $this->tenant())
            ->where('cliente_id', $data['cliente_id'])
            ->first();

        if (!$cuenta) {
            $cuenta = LealtadCuenta::create([
                'tenant_id' => $this->tenant(),
                'cliente_id' => $data['cliente_id'],
                'nivel' => 'bronce',
                'tasa_cambio' => 1,
            ]);
        }

        $data = [
            'cuenta' => [
                'id' => $cuenta->id,
                'puntos_acumulados' => $cuenta->puntos_acumulados,
                'puntos_canjeados' => $cuenta->puntos_canjeados,
                'puntos_vencidos' => $cuenta->puntos_vencidos,
                'puntos_disponibles' => $cuenta->puntosDisponibles(),
                'nivel' => $cuenta->nivel,
                'tasa_cambio' => $cuenta->tasa_cambio,
                'ultima_actividad' => $cuenta->ultima_actividad,
            ],
            'recompensas' => [],
        ];

        // Calcular recompensas disponibles según nivel
        $puntosDisp = $cuenta->puntosDisponibles();
        $data['recompensas'] = [
            ['nombre' => '5% Descuento', 'costo_puntos' => 100, 'descuento' => 0.05],
            ['nombre' => '10% Descuento', 'costo_puntos' => 200, 'descuento' => 0.10],
            ['nombre' => '15% Descuento', 'costo_puntos' => 300, 'descuento' => 0.15],
            ['nombre' => 'Gratis Envio', 'costo_puntos' => 150, 'descuento' => 'envio_gratis'],
        ];

        return $this->success($data);
    }

    public function canjear(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cart_id' => 'required|exists:carts,id',
            'recompensa_id' => 'required|string',
        ]);

        $cuenta = LealtadCuenta::where('tenant_id', $this->tenant())
            ->where('cliente_id', $data['cliente_id'])
            ->firstOrFail();

        $recompensa = match ($data['recompensa_id']) {
            '100' => ['costo' => 100, 'descuento' => 0.05, 'nombre' => '5% Descuento'],
            '200' => ['costo' => 200, 'descuento' => 0.10, 'nombre' => '10% Descuento'],
            '300' => ['costo' => 300, 'descuento' => 0.15, 'nombre' => '15% Descuento'],
            '150' => ['costo' => 150, 'descuento' => 'envio_gratis', 'nombre' => 'Gratis Envio'],
            default => null,
        };

        if (!$recompensa) {
            return $this->error('Invalid reward.', 'invalid_reward');
        }

        if (!$cuenta->puedeCanjear($recompensa['costo'])) {
            return $this->error('Not enough points. Available: ' . $cuenta->puntosDisponibles(), 'insufficient_points');
        }

        return DB::transaction(function () use ($cuenta, $recompensa, $request) {
            $cuenta->canjearPuntos($recompensa['costo']);

            LealtadMovimiento::create([
                'cuenta_id' => $cuenta->id,
                'tipo' => 'canjear',
                'cantidad' => $recompensa['costo'],
                'notas' => 'Canjeado: ' . $recompensa['nombre'],
            ]);

            // Si es descuento, aplicar al carrito
            $cart = Cart::with('items.producto')
                ->where('tenant_id', $this->tenant())
                ->findOrFail($request->cart_id);

            if (is_numeric($recompensa['descuento'])) {
                $descuento = $cart->subtotalItems() * $recompensa['descuento'];
                $cart->applyPromo($descuento, 'porcentaje');
            }

            return $this->success([
                'cuenta' => [
                    'puntos_disponibles' => $cuenta->puntosDisponibles(),
                    'nivel' => $cuenta->nivel,
                ],
                'recompensa' => [
                    'nombre' => $recompensa['nombre'],
                    'descuento' => $recompensa['descuento'],
                    'puntos_gastados' => $recompensa['costo'],
                ],
            ], 'Reward redeemed successfully.');
        });
    }

    public function historial(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'limite' => 'sometimes|integer|min:1|max:50',
        ]);

        $cuenta = LealtadCuenta::where('tenant_id', $this->tenant())
            ->where('cliente_id', $data['cliente_id'])
            ->firstOrFail();

        $movimientos = LealtadMovimiento::where('cuenta_id', $cuenta->id)
            ->with(['venta:id,ncf,total'])
            ->orderBy('created_at', 'desc')
            ->limit($data['limite'] ?? 20)
            ->get();

        return $this->success([
            'movimientos' => $movimientos,
            'total' => $movimientos->count(),
        ]);
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
