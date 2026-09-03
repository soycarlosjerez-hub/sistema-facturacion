<?php

namespace App\Services;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\SesionCaja;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

/**
 * Servicio especializado en la gestión de pagos de ventas.
 * 
 * Responsable: crear registros de pago (Pago), procesar pagos
 * únicos y mixtos (efectivo, tarjeta, transferencia), actualizar
 * el estado del cliente (deuda), y actualizar las cajas por método.
 * 
 * Es invocado por el SaleCreateService y SaleCancelService para
 * la lógica de flujo de efectivo.
 */
class SalePaymentService
{
    /**
     * Procesar el pago de una venta completada.
     * 
     * @param Venta $venta La venta a pagar
     * @param SesionCaja $sesion Sesión de caja activa
     * @param string $metodoPago Método de pago ('efectivo', 'tarjeta', 'transferencia')
     * @param float $monto Monto a pagar
     * @return Pago El pago creado
     */
    public function procesarPagoUnico(Venta $venta, SesionCaja $sesion, string $metodoPago, float $monto): Pago
    {
        $pago = Pago::create([
            'tenant_id'      => Auth::user()->business_instance_id,
            'venta_id'       => $venta->id,
            'caja_id'        => $sesion->caja_id,
            'sesion_caja_id' => $sesion->id,
            'monto'          => $monto,
            'metodo_pago'    => $metodoPago,
            'nota'           => 'Pago automático (Venta ' . ucfirst($metodoPago) . ')',
            'fecha_pago'     => now(),
        ]);

        $this->actualizarCajaPorMetodo($sesion, $metodoPago, $monto, true);

        Event::dispatch(new \App\Events\PaymentReceived($pago));

        return $pago;
    }

    /**
     * Procesar un pago mixto con múltiples métodos de pago.
     * 
     * @param Venta $venta La venta a pagar
     * @param SesionCaja $sesion Sesión de caja activa
     * @param array $metodos Pagos mixtos ['efectivo' => monto, 'tarjeta' => monto, ...]
     * @param float $total Total de la venta para validación
     * @return Pago[] Array de pagos creados
     * @throws \Exception Si la suma no coincide con el total
     */
    public function procesarPagoMixto(Venta $venta, SesionCaja $sesion, array $metodos, float $total): array
    {
        $mixtoSum = array_sum($metodos);

        if (abs($mixtoSum - $total) > 0.02) {
            throw new \Exception(
                "La suma de los montos mixtos (RD$ " . number_format($mixtoSum, 2) .
                ") debe ser igual al total (RD$ " . number_format($total, 2) . ")."
            );
        }

        $pagos = [];

        foreach ($metodos as $tipo => $monto) {
            if ($monto <= 0) {
                continue;
            }

            $pago = Pago::create([
                'tenant_id'      => Auth::user()->business_instance_id,
                'venta_id'       => $venta->id,
                'caja_id'        => $sesion->caja_id,
                'sesion_caja_id' => $sesion->id,
                'monto'          => $monto,
                'metodo_pago'    => $tipo,
                'nota'           => 'Pago mixto (' . ucfirst($tipo) . ')',
                'fecha_pago'     => now(),
            ]);

            $this->actualizarCajaPorMetodo($sesion, $tipo, $monto, true);

            Event::dispatch(new \App\Events\PaymentReceived($pago));

            $pagos[] = $pago;
        }

        return $pagos;
    }

    /**
     * Asignar o actualizar una venta a la cuenta abierta del cliente.
     * Incrementa el balance pendiente del cliente.
     * 
     * @param int $clienteId ID del cliente
     * @param float $monto Monto a agregar a la deuda
     * @param string $clienteNombre Nombre del cliente (para evitar consumidior final)
     * @return bool True si se asignó la deuda al cliente
     */
    public function asignarDeudaCliente(int $clienteId, float $monto, string $clienteNombre): bool
    {
        if ($clienteNombre === 'Consumidor Final') {
            return false;
        }

        $cliente = Cliente::find($clienteId);

        if ($cliente) {
            $cliente->increment('balance_pendiente', $monto);
            return true;
        }

        return false;
    }

    /**
     * Devolver el monto de una venta a la caja al anular.
     * 
     * @param SesionCaja $sesion Sesión de caja
     * @param array $pagos Array de pagos de la venta (['metodo_pago' => monto, ...])
     */
    public function decrementarCajaPorAnulacion(SesionCaja $sesion, array $pagos): void
    {
        foreach ($pagos as $pago) {
            $this->actualizarCajaPorMetodo($sesion, $pago->metodo_pago, $pago->monto, false);
        }
    }

    /**
     * Devolver el balance pendiente de un cliente al anular venta.
     * 
     * @param int $clienteId ID del cliente
     * @param Venta $venta La venta a anular
     * @return void
     */
    public function devolverDeudaCliente(int $clienteId, Venta $venta): void
    {
        if (in_array($venta->estado, ['pendiente', 'cuenta_abierta'])) {
            $montoDeuda = $venta->total - $venta->montoPagado();

            if ($montoDeuda > 0) {
                Cliente::where('id', $clienteId)->decrement('balance_pendiente', $montoDeuda);
            }
        }
    }

    /**
     * Actualizar las ventas de una caja por método de pago.
     * 
     * @param SesionCaja $sesion Sesión de caja
     * @param string $metodoPago Método de pago
     * @param float $monto Monto
     * @param bool $incrementar True para sumar, false para restar
     */
    public function actualizarCajaPorMetodo(SesionCaja $sesion, string $metodoPago, float $monto, bool $incrementar = true): void
    {
        $operacion = $incrementar ? 'increment' : 'decrement';

        match ($metodoPago) {
            'efectivo'      => $sesion->{$operacion}('ventas_efectivo', $monto),
            'tarjeta'       => $sesion->{$operacion}('ventas_tarjeta', $monto),
            'transferencia' => $sesion->{$operacion}('ventas_transferencia', $monto),
            default         => null,
        };
    }

    /**
     * Obtener los montos mixtos normalizados de los datos de la venta.
     * 
     * @param array $data Datos de la venta con campos mixto_*
     * @return array ['efectivo' => float, 'tarjeta' => float, 'transferencia' => float]
     */
    public function normalizarPagoMixto(array $data): array
    {
        return [
            'efectivo'      => (float) ($data['mixto_efectivo'] ?? 0),
            'tarjeta'       => (float) ($data['mixto_tarjeta'] ?? 0),
            'transferencia' => (float) ($data['mixto_transferencia'] ?? 0),
        ];
    }

    /**
     * Verificar que la suma de pagos mixtos coincida con el total.
     * 
     * @param array $metodos Metodos de pago mixtos ['tipo' => monto, ...]
     * @param float $total Total de la venta
     * @return bool True si coincide (dentro de 0.02)
     */
    public function validarPagoMixto(array $metodos, float $total): bool
    {
        $sum = array_sum($metodos);
        return abs($sum - $total) <= 0.02;
    }
}
