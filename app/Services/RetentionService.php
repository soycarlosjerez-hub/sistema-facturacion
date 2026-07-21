<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\Cliente;
use App\Support\RetencionCalculator;
use Illuminate\Support\Facades\DB;

/**
 * Servicio centralizado para cálculo y gestión de retenciones
 * conforme normativa DGII de República Dominicana.
 */
class RetentionService
{
    /**
     * Calcula las retenciones automáticas para una COMPRA.
     * 
     * @param Compra $compra La compra a procesar
     * @param Proveedor $proveedor El proveedor de la compra
     * @return array Retenciones calculadas [itbis, isr]
     */
    public function calcularRetencionesCompra(Compra $compra, Proveedor $proveedor): array
    {
        $resultados = [
            'itbis' => [
                'monto' => 0,
                'aplica' => false,
                'tasa' => 0,
            ],
            'isr' => [
                'monto' => 0,
                'aplica' => false,
                'tasa' => 0,
                'tipo_pago' => 'servicios',
            ],
        ];

        // --- Retención ITBIS ---
        if ($compra->aplica_retencion_itbis && $compra->itbis_total > 0) {
            $calc = RetencionCalculator::calcularRetencionItbisComprador(
                (float) $compra->itbis_total,
                $proveedor->sujeto_retencion_itbis
            );

            if ($calc['aplica']) {
                $resultados['itbis'] = [
                    'monto' => $calc['monto_retenido'],
                    'aplica' => true,
                    'tasa' => $calc['tasa'],
                ];
            }
        }

        // --- Retención ISR ---
        if ($compra->aplica_retencion_isr && $compra->subtotal > 0) {
            $tipoPersona = $proveedor->tipo_persona ?? 'juridica';
            $tipoPago = $this->determinarTipoPagoCompra($compra);
            
            // Calcular acumulado mensual del proveedor
            $acumuladoMes = $this->getAcumuladoMensualProveedor(
                $proveedor->id,
                now()->month,
                now()->year
            );

            $calc = RetencionCalculator::calcularRetencionIsr(
                (float) $compra->subtotal,
                $tipoPersona,
                $tipoPago,
                $acumuladoMes
            );

            if ($calc['aplica']) {
                $resultados['isr'] = [
                    'monto' => $calc['monto_retenido'],
                    'aplica' => true,
                    'tasa' => $calc['tasa'],
                    'tipo_pago' => $tipoPago,
                    'base_legal' => $calc['base_legal'] ?? 'Art. 228-AP',
                    'base_calculo' => $calc['base_calculo'] ?? (float) $compra->subtotal,
                ];
            }
        }

        return $resultados;
    }

    /**
     * Calcula las retenciones para una VENTA (cuando vendemos a cliente sujeto).
     * 
     * @param Venta $venta La venta a procesar
     * @param Cliente $cliente El cliente de la venta
     * @return array Retenciones calculadas
     */
    public function calcularRetencionesVenta(Venta $venta, Cliente $cliente): array
    {
        $resultados = [
            'itbis_vendedor' => [
                'monto' => 0,
                'aplica' => false,
                'tasa' => 0,
            ],
        ];

        // Cuando vendemos a consumidor final o no responsable,
        // nosotros retenemos 100% del ITBIS
        if ($venta->impuestos > 0) {
            // Verificar si el cliente es sujeto a retención
            $esSujeto = $this->esClienteSujetoARetencion($cliente);

            if ($esSujeto) {
                $calc = RetencionCalculator::calcularRetencionItbisVendedor(
                    (float) $venta->impuestos,
                    $esSujeto
                );

                if ($calc['aplica']) {
                    $resultados['itbis_vendedor'] = [
                        'monto' => $calc['monto_retenido'],
                        'aplica' => true,
                        'tasa' => $calc['tasa'],
                    ];
                }
            }
        }

        return $resultados;
    }

    /**
     * Guarda las retenciones calculadas en la compra.
     */
    public function guardarRetencionesCompra(Compra $compra, array $retenciones): void
    {
        $itbisMonto = $retenciones['itbis']['monto'] ?? 0;
        $isrMonto = $retenciones['isr']['monto'] ?? 0;
        $totalNeto = (float) $compra->total - $itbisMonto - $isrMonto;

        $compra->update([
            'retencion_itbis' => $itbisMonto,
            'retencion_isr' => $isrMonto,
            'total_neto' => $totalNeto,
        ]);
    }

    /**
     * Obtiene el acumulado mensual de un proveedor para cálculo ISR.
     */
    public function getAcumuladoMensualProveedor(int $proveedorId, int $mes, int $anio): float
    {
        return Compra::where('proveedor_id', $proveedorId)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->whereNotNull('deleted_at') // Solo compras no anuladas
            ->sum('subtotal');
    }

    /**
     * Determina el tipo de pago ISR para una compra.
     */
    private function determinarTipoPagoCompra(Compra $compra): string
    {
        // Se podría determinar basado en tipo_compra_id o categoría de producto
        // Por defecto retornamos servicios
        return 'servicios';
    }

    /**
     * Verifica si un cliente es sujeto a retención en ventas.
     */
    private function esClienteSujetoARetencion(Cliente $cliente): bool
    {
        // Lógica: si el cliente es Contribuyente Especial (10-10),
        // entonces NO retenemos (él se retiene solo)
        // Si es Consumidor Final u Ordinario, retenemos
        
        $tipoCliente = $cliente->tipo_cliente ?? 'consumo';
        
        // Consumidores finales no son sujetos
        if ($tipoCliente === 'consumo') {
            return false;
        }

        // Aquí se podría verificar el régimen fiscal del cliente
        // Por ahora, asumimos que clientes registrados son sujetos
        return !empty($cliente->rnc_cedula);
    }

    /**
     * Genera un resumen de retenciones por período para Formulario 14-14.
     */
    public function generarResumenRetenciones(int $mes, int $anio): array
    {
        $compras = Compra::whereMonth('fecha', $mes)
            ->whereYear('fecha', $anio)
            ->whereNotNull('deleted_at')
            ->with(['proveedor'])
            ->get();

        $ventas = Venta::whereMonth('created_at', $mes)
            ->whereYear('created_at', $anio)
            ->whereNotNull('deleted_at')
            ->where('estado', 'completada')
            ->with(['cliente'])
            ->get();

        $resumen = [
            'periodo' => sprintf('%04d-%02d', $anio, $mes),
            'itbis_compras' => [
                'total_retenido' => $compras->sum('retencion_itbis'),
                'cantidad_compras' => $compras->where('retencion_itbis', '>', 0)->count(),
                'detalles' => [],
            ],
            'itbis_ventas' => [
                'total_retenido' => 0,
                'cantidad_ventas' => 0,
                'detalles' => [],
            ],
            'isr_compras' => [
                'total_retenido' => $compras->sum('retencion_isr'),
                'cantidad_compras' => $compras->where('retencion_isr', '>', 0)->count(),
                'detalles' => [],
            ],
        ];

        // Detalles de compras
        foreach ($compras as $compra) {
            if ($compra->retencion_itbis > 0 || $compra->retencion_isr > 0) {
                $resumen['itbis_compras']['detalles'][] = [
                    'compra_id' => $compra->id,
                    'proveedor' => $compra->proveedor->nombre,
                    'rnc' => $compra->proveedor->rnc,
                    'itbis_retenido' => $compra->retencion_itbis,
                    'isr_retenido' => $compra->retencion_isr,
                    'fecha' => $compra->fecha->format('Y-m-d'),
                ];
            }
        }

        return $resumen;
    }
}
