<?php

namespace App\Services;

use App\Enums\OrdenState;
use App\Enums\OrdenTipo;
use App\Models\Caja;
use App\Models\Orden;
use App\Models\Pago;
use App\Models\SesionCaja;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdenPaymentService
{
    public function procesarPago(Orden $orden, array $data): array
    {
        if (in_array($orden->estado, [OrdenState::Completada->value, OrdenState::Anulada->value])) {
            return ['error' => 'La orden ya está ' . $orden->estado, 'code' => 422];
        }

        $caja = Caja::where('nombre', 'órdenes')
            ->where('activo', true)
            ->first();

        if (!$caja) {
            $caja = Caja::create([
                'nombre'   => 'órdenes',
                'codigo'   => 'ORD',
                'sucursal_id' => Auth::user()->sucursal_id,
                'tenant_id'    => Auth::user()->business_instance_id,
                'activo'   => true,
                'estado'   => 'cerrada',
            ]);
        }

        $sesion = SesionCaja::where('caja_id', $caja->id)
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            $sesion = SesionCaja::create([
                'tenant_id'      => Auth::user()->business_instance_id,
                'caja_id'        => $caja->id,
                'user_id'        => Auth::id(),
                'fecha_apertura' => now(),
                'monto_inicial'  => 0,
                'estado'         => 'abierta',
            ]);
            $caja->update(['estado' => 'abierta']);
        }

        $propina = (float)($data['propina'] ?? 0);
        $servicioPorcentaje = (float) SystemSetting::get('servicio_porcentaje', 0);
        $cargoServicio = (bool)($data['cargo_servicio'] ?? false) ? round($orden->subtotal * $servicioPorcentaje / 100, 2) : 0;
        $totalConPropina = $orden->subtotal + $orden->impuestos + $propina + $cargoServicio - $orden->descuento;
        $metodo = $data['metodo_pago'];

        if ($metodo === 'mixto') {
            $sumaPagos = (float)($data['monto_recibido'] ?? 0)
                       + (float)($data['monto_tarjeta'] ?? 0)
                       + (float)($data['monto_transferencia'] ?? 0);
            if (abs($sumaPagos - $totalConPropina) > 0.01) {
                return ['error' => "La suma de los pagos no cubre el total de RD$ " . number_format($totalConPropina, 2), 'code' => 422];
            }
        } elseif ($metodo !== 'fiado') {
            $montoColumna = match ($metodo) {
                'efectivo' => $data['monto_recibido'] ?? 0,
                'tarjeta' => $data['monto_tarjeta'] ?? 0,
                'transferencia' => $data['monto_transferencia'] ?? 0,
                default => 0,
            };
            if ((float)$montoColumna < $totalConPropina - 0.01) {
                $label = match ($metodo) {
                    'efectivo' => 'recibido',
                    'tarjeta' => 'de tarjeta',
                    'transferencia' => 'de transferencia',
                    default => '',
                };
                return ['error' => "El monto {$label} no cubre el total de RD$ " . number_format($totalConPropina, 2), 'code' => 422];
            }
        }

        DB::beginTransaction();
        try {
            $orden->update([
                'estado'         => OrdenState::Completada->value,
                'propina'        => $propina,
                'cargo_servicio' => $cargoServicio,
            ]);

            if ($metodo === 'mixto') {
                foreach ([
                    ['metodo' => 'efectivo', 'monto' => $data['monto_recibido'] ?? 0],
                    ['metodo' => 'tarjeta', 'monto' => $data['monto_tarjeta'] ?? 0],
                    ['metodo' => 'transferencia', 'monto' => $data['monto_transferencia'] ?? 0],
                ] as $pago) {
                    if ($pago['monto'] > 0) {
                        Pago::create([
                            'tenant_id'      => Auth::user()->business_instance_id,
                            'orden_id'       => $orden->id,
                            'caja_id'        => $sesion->caja_id,
                            'sesion_caja_id' => $sesion->id,
                            'monto'          => $pago['monto'],
                            'metodo_pago'    => $pago['metodo'],
                            'nota'           => 'Pago Orden #' . $orden->id,
                            'fecha_pago'     => now(),
                        ]);
                    }
                }
            } else {
                Pago::create([
                    'tenant_id'      => Auth::user()->business_instance_id,
                    'orden_id'       => $orden->id,
                    'caja_id'        => $sesion->caja_id,
                    'sesion_caja_id' => $sesion->id,
                    'monto'          => $totalConPropina,
                    'metodo_pago'    => $metodo,
                    'nota'           => 'Pago Orden #' . $orden->id,
                    'fecha_pago'     => now(),
                ]);
            }

            DB::commit();

            $orden->load('detalles.producto', 'cliente', 'pagos');
            return ['success' => true, 'orden' => $orden];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }
}
