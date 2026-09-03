<?php

namespace App\Services;

use App\Models\LealtadCuenta;
use App\Models\LealtadMovimiento;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class LealtadService
{
    private const RECOMPENSAS = [
        '100' => ['costo' => 100, 'descuento' => 0.05, 'nombre' => '5% Descuento'],
        '200' => ['costo' => 200, 'descuento' => 0.10, 'nombre' => '10% Descuento'],
        '300' => ['costo' => 300, 'descuento' => 0.15, 'nombre' => '15% Descuento'],
        '150' => ['costo' => 150, 'descuento' => 'envio_gratis', 'nombre' => 'Envío Gratis'],
    ];

    public function obtenerOCrearCuenta(int $tenantId, int $clienteId): LealtadCuenta
    {
        return LealtadCuenta::firstOrCreate(
            ['tenant_id' => $tenantId, 'cliente_id' => $clienteId],
            ['nivel' => 'bronce', 'tasa_cambio' => 1]
        );
    }

    public function consultarPuntos(int $tenantId, int $clienteId): array
    {
        $cuenta = $this->obtenerOCrearCuenta($tenantId, $clienteId);

        return [
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
            'recompensas' => array_values(self::RECOMPENSAS),
        ];
    }

    public function ganarPuntos(int $tenantId, int $clienteId, int $puntos, ?int $ventaId = null): array
    {
        $cuenta = $this->obtenerOCrearCuenta($tenantId, $clienteId);

        $cuenta->ganarPuntos($puntos);

        LealtadMovimiento::create([
            'cuenta_id' => $cuenta->id,
            'tipo' => 'ganar',
            'cantidad' => $puntos,
            'venta_id' => $ventaId,
            'notas' => $ventaId ? "Puntos ganados en venta #{$ventaId}" : 'Puntos ganados',
        ]);

        return [
            'puntos_ganados' => $puntos,
            'puntos_disponibles' => $cuenta->puntosDisponibles(),
            'nivel' => $cuenta->nivel,
        ];
    }

    public function canjearPuntos(int $tenantId, int $clienteId, string $recompensaId, ?Cart $cart = null): array
    {
        $recompensa = self::RECOMPENSAS[$recompensaId] ?? null;

        if (!$recompensa) {
            return ['valido' => false, 'error' => 'Recompensa inválida.'];
        }

        $cuenta = $this->obtenerOCrearCuenta($tenantId, $clienteId);

        if (!$cuenta->puedeCanjear($recompensa['costo'])) {
            return [
                'valido' => false,
                'error' => 'Puntos insuficientes. Disponibles: ' . $cuenta->puntosDisponibles(),
                'code' => 'insufficient_points',
            ];
        }

        return DB::transaction(function () use ($cuenta, $recompensa, $cart) {
            $cuenta->canjearPuntos($recompensa['costo']);

            LealtadMovimiento::create([
                'cuenta_id' => $cuenta->id,
                'tipo' => 'canjear',
                'cantidad' => $recompensa['costo'],
                'notas' => 'Canjeado: ' . $recompensa['nombre'],
            ]);

            if ($cart && is_numeric($recompensa['descuento'])) {
                $descuento = $cart->subtotalItems() * $recompensa['descuento'];
                $cart->applyPromo($descuento, 'porcentaje');
            }

            return [
                'valido' => true,
                'puntos_disponibles' => $cuenta->puntosDisponibles(),
                'nivel' => $cuenta->nivel,
                'recompensa' => $recompensa['nombre'],
                'puntos_gastados' => $recompensa['costo'],
            ];
        });
    }

    public function historial(int $tenantId, int $clienteId, int $limite = 20): array
    {
        $cuenta = $this->obtenerOCrearCuenta($tenantId, $clienteId);

        $movimientos = LealtadMovimiento::where('cuenta_id', $cuenta->id)
            ->with(['venta:id,ncf,total'])
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();

        return [
            'movimientos' => $movimientos,
            'total' => $movimientos->count(),
        ];
    }
}
