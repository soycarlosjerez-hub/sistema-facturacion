<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

/**
 * Servicio especializado en cálculos financieros de ventas.
 * 
 * Responsable: ITBIS, descuentos (línea y general), total final,
 * determinación de estado según método de pago.
 * 
 * Se mantiene como singleton en el Service Container y es inyectado
 * por SaleService y otros servicios que necesiten cálculos.
 */
class SaleCalcService
{
    /**
     * Roles que pueden sobreescribir precios y descuentos > 50%.
     * 
     * @return string[]
     */
    public function autorizadosPrecios(): array
    {
        return ['admin', 'admin-business', 'root', 'gerente'];
    }

    /**
     * Determinar si el usuario actual puede modificar precios.
     * 
     * @return bool
     */
    public function puedeSobreescribirPrecio(): bool
    {
        $roles = $this->autorizadosPrecios();

        return in_array(Auth::user()->role, $roles, true)
            || Auth::user()->hasRole($roles);
    }

    /**
     * Calcular el ITBIS recalculado sobre la base bruta autoritativa.
     * Aplica descuentos por línea y general proporcionalmente.
     * 
     * @param array $lineas Array de líneas de venta normalizadas
     * @param float $generalDescuento Descuento general sobre el subtotal total
     * @param float $subtotalTotal Subtotal total antes de descuentos
     * @return float ITBIS recalculado (2 decimales)
     */
    public function calcularItbisRecalculado(array $lineas, float $generalDescuento = 0.0, float $subtotalTotal = 0.0): float
    {
        $itbisRecalculado = 0.0;

        foreach ($lineas as $line) {
            $descAplicado = $line['tipo'] === 'porcentaje'
                ? $line['subtotal'] * min($line['desc'], 100) / 100
                : $line['desc'];

            $baseFinal = max(0, $line['subtotal'] - $descAplicado);

            if ($generalDescuento > 0 && $subtotalTotal > 0) {
                $proporcion = $baseFinal / $subtotalTotal;
                $baseFinal = max(0, $baseFinal - ($generalDescuento * $proporcion));
            }

            $tasaItbis = $line['sin_itbis'] ? 0 : $line['itbis_p'];
            $itbisRecalculado += $baseFinal * ($tasaItbis / 100);
        }

        return round($itbisRecalculado, 2);
    }

    /**
     * Calcular el total de descuentos por línea.
     * 
     * @param array $lineas Array de líneas de venta normalizadas
     * @return float Total de descuentos por línea
     */
    public function calcularDescuentosLinea(array $lineas): float
    {
        $total = 0.0;

        foreach ($lineas as $linea) {
            if ($linea['desc'] <= 0) {
                continue;
            }

            $total += $linea['tipo'] === 'porcentaje'
                ? $linea['subtotal'] * min($linea['desc'], 100) / 100
                : $linea['desc'];
        }

        return $total;
    }

    /**
     * Verificar si el porcentaje de descuento total supera el 50%.
     * 
     * @param float $subtotalTotal Subtotal antes de descuentos
     * @param float $totalDescuentos Suma de descuentos (línea + general)
     * @param bool $puedeSobreescribir Si el usuario tiene permiso para exceder el límite
     * @return bool True si los descuentos exceden el 50% sin autorización
     * @throws \Exception Si los descuentos superan el 50% sin autorización
     */
    public function verificarLimiteDescuentos(float $subtotalTotal, float $totalDescuentos, bool $puedeSobreescribir): void
    {
        if ($subtotalTotal > 0) {
            $pctDescuento = (($totalDescuentos) / $subtotalTotal) * 100;

            if ($pctDescuento > 50 && !$puedeSobreescribir) {
                throw new \Exception('Descuentos superiores al 50% requieren autorización de administrador.');
            }
        }
    }

    /**
     * Calcular el total final de la venta: subtotal - descuentos + ITBIS.
     * 
     * @param float $subtotalTotal Subtotal antes de descuentos
     * @param float $descuentosLinea Descuentos por línea
     * @param float $generalDescuento Descuento general
     * @param float $itbisRecalculado ITBIS calculado
     * @return float Total final redondeado a 2 decimales
     */
    public function calcularTotalFinal(float $subtotalTotal, float $descuentosLinea, float $generalDescuento, float $itbisRecalculado): float
    {
        return round($subtotalTotal - ($descuentosLinea + $generalDescuento) + $itbisRecalculado, 2);
    }

    /**
     * Determinar el estado de la venta según el método de pago.
     * 
     * @param string $metodoPago Método de pago ('efectivo', 'tarjeta', 'fiado', 'cuenta_abierta', etc.)
     * @return string Estado de la venta ('completada', 'pendiente', 'cuenta_abierta')
     */
    public function determinarEstadoVenta(string $metodoPago): string
    {
        return match ($metodoPago) {
            'fiadi' => 'pendiente',
            'cuenta_abierta' => 'cuenta_abierta',
            default => 'completada',
        };
    }

    /**
     * Obtener el porcentaje de ITBIS de la instancia actual del usuario.
     * 
     * @return float Porcentaje de ITBIS
     */
    public function itbisPorcentajeInstancia(): float
    {
        $user = Auth::user();
        $config = $user?->businessInstance?->configuracion ?? [];

        return (float) ($config['itbis_porcentaje'] ?? SystemSetting::itbisDefault());
    }
}
