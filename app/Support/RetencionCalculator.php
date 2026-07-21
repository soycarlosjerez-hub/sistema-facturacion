<?php

namespace App\Support;

/**
 * Calculadora de retenciones conforme normativa DGII República Dominicana.
 * 
 * Referencias legales:
 * - Art. 228-AP: Retención ISR sobre pagos
 * - Art. 228-AQ: Retención ISR sobre compras a no responsables
 * - Art. 228-AT: Retención ISR sobre pagos al exterior
 * - Resolución 10-2013: Retención ITBIS
 */
class RetencionCalculator
{
    // ==================== ITBIS ====================
    
    const ITBIS_ALICUOTA_GENERAL = 18;
    const ITBIS_ALICUOTA_REDUCIDA = 5;
    const ITBIS_EXENTO = 0;

    // Tasa de retención ITBIS sobre el ITBIS facturado
    const ITBIS_RETENCION_COMPRADOR_PORC = 100; // Comprador retiene 100% del ITBIS
    const ITBIS_RETENCION_VENDEDOR_PORC = 100;  // Vendedor retiene 100% del ITBIS

    // Monto mínimo para retención ITBIS (RD$)
    const ITBIS_MONTO_MINIMO = 0;

    // ==================== ISR ====================
    
    // Tasas de retención ISR según tipo de pago
    const ISR_HONORARIOS_PROFESSIONALES = 2;      // Art. 228-AP: 2%
    const ISR_ARRENDAMIENTOS = 10;                 // Art. 228-AP: 10%
    const ISR_COMISIONES = 2;                      // Art. 228-AP: 2%
    const ISR_SERVICIOS_GENERALES = 2;             // Art. 228-AP: 2%
    const ISR_COMPRAS_NO_RESPONSABLE = 3;          // Art. 228-AQ: 3%
    const ISR_SERVICIOS_NO_RESPONSABLE = 2;        // Art. 228-AQ: 2%
    const ISR_DIVIDIDOS_UTILIDADES = 5;            // Art. 228-AR: 5%
    const ISR_PAGOS_EXTRANJERO = 10;               // Art. 228-AT: 10% (variable según tratado)

    // Tasas ISR estándar
    const ISR_TASA_PERSONA_JURIDICA = 10;          // 10% desde el primer peso
    const ISR_TASA_PERSONA_FISICA = 10;            // 10% sobre excedente

    // Umbrales ISR (actualizados 2026)
    const ISR_LIMITE_PERSONA_FISICA = 50000;       // Exento hasta RD$50,000 mensuales

    // ==================== TIPOS DE PAGO ISR ====================
    
    const TIPO_PAGO_HONORARIOS = 'honorarios';
    const TIPO_PAGO_ARRENDAMIENTO = 'arrendamiento';
    const TIPO_PAGO_COMISION = 'comision';
    const TIPO_PAGO_SERVICIOS = 'servicios';
    const TIPO_PAGO_COMPRAS = 'compras';
    const TIPO_PAGO_DIVIDENDOS = 'dividendos';
    const TIPO_PAGO_EXTRANJERO = 'extranjero';

    /**
     * Calcula la retención ITBIS para COMPRADOR (quien compra).
     * El comprador retiene 100% del ITBIS facturado por el vendedor.
     * 
     * @param float $itbis_facturado Monto de ITBIS de la factura
     * @param bool $sujeto_a_retencion Si el proveedor está en lista de sujetos a retención
     * @return array Resultado con monto retenido, tasa, etc.
     */
    public static function calcularRetencionItbisComprador(float $itbis_facturado, bool $sujeto_a_retencion = true): array
    {
        if (!$sujeto_a_retencion || $itbis_facturado <= self::ITBIS_MONTO_MINIMO) {
            return [
                'monto_retenido' => 0,
                'tasa' => 0,
                'itbis_neto' => $itbis_facturado,
                'aplica' => false,
                'tipo_retencion' => 'ITBIS_COMPRADOR',
            ];
        }

        $retenido = round($itbis_facturado * (self::ITBIS_RETENCION_COMPRADOR_PORC / 100), 2);

        return [
            'monto_retenido' => $retenido,
            'tasa' => self::ITBIS_RETENCION_COMPRADOR_PORC,
            'itbis_neto' => round($itbis_facturado - $retenido, 2),
            'aplica' => true,
            'tipo_retencion' => 'ITBIS_COMPRADOR',
        ];
    }

    /**
     * Calcula la retención ITBIS para VENDEDOR (quien vende).
     * El vendedor retiene 100% del ITBIS cuando vende a consumidor final o no responsable.
     * 
     * @param float $itbis_facturado Monto de ITBIS de la factura
     * @param bool $sujeto_a_retencion Si el cliente está en lista de sujetos a retención
     * @return array Resultado con monto retenido, tasa, etc.
     */
    public static function calcularRetencionItbisVendedor(float $itbis_facturado, bool $sujeto_a_retencion = true): array
    {
        if (!$sujeto_a_retencion || $itbis_facturado <= self::ITBIS_MONTO_MINIMO) {
            return [
                'monto_retenido' => 0,
                'tasa' => 0,
                'itbis_neto' => $itbis_facturado,
                'aplica' => false,
                'tipo_retencion' => 'ITBIS_VENDEDOR',
            ];
        }

        $retenido = round($itbis_facturado * (self::ITBIS_RETENCION_VENDEDOR_PORC / 100), 2);

        return [
            'monto_retenido' => $retenido,
            'tasa' => self::ITBIS_RETENCION_VENDEDOR_PORC,
            'itbis_neto' => round($itbis_facturado - $retenido, 2),
            'aplica' => true,
            'tipo_retencion' => 'ITBIS_VENDEDOR',
        ];
    }

    /**
     * Calcula la retención ISR según tipo de pago y tipo de proveedor.
     * 
     * @param float $monto_total Monto total del pago/factura
     * @param string $tipo_proveedor 'juridica' o 'fisica'
     * @param string $tipo_pago Tipo de pago (honorarios, arrendamiento, comisiones, etc.)
     * @param float $monto_acumulado_mes Monto acumulado en el mes (para persona física)
     * @return array Resultado con monto retenido, tasa, base legal, etc.
     */
    public static function calcularRetencionIsr(
        float $monto_total,
        string $tipo_proveedor = 'juridica',
        string $tipo_pago = self::TIPO_PAGO_SERVICIOS,
        float $monto_acumulado_mes = 0
    ): array {
        $es_juridica = strtolower($tipo_proveedor) === 'juridica';
        
        // Determinar tasa según tipo de pago
        $tasa = self::getTasaIsrPorTipoPago($tipo_pago, $es_juridica);

        if (!$es_juridica) {
            // Persona física: exento hasta RD$50,000 mensuales
            $monto_total_periodo = $monto_total + $monto_acumulado_mes;
            
            if ($monto_total_periodo <= self::ISR_LIMITE_PERSONA_FISICA) {
                return [
                    'monto_retenido' => 0,
                    'tasa' => $tasa,
                    'aplica' => false,
                    'excede_limite' => false,
                    'monto_acumulado' => $monto_total_periodo,
                    'tipo_retencion' => 'ISR_PERSONA_FISICA',
                    'base_legal' => 'Art. 228-AP',
                ];
            }

            // Retener solo sobre el excedente
            $base_retencion = max(0, $monto_total_periodo - self::ISR_LIMITE_PERSONA_FISICA);
            $retenido = round($base_retencion * ($tasa / 100), 2);

            return [
                'monto_retenido' => $retenido,
                'tasa' => $tasa,
                'aplica' => $retenido > 0,
                'excede_limite' => true,
                'base_calculo' => $base_retencion,
                'monto_acumulado' => $monto_total_periodo,
                'tipo_retencion' => 'ISR_PERSONA_FISICA',
                'base_legal' => 'Art. 228-AP',
            ];
        }

        // Persona jurídica: 10% desde el primer peso
        $base_retencion = $monto_total;
        $retenido = round($base_retencion * ($tasa / 100), 2);

        return [
            'monto_retenido' => $retenido,
            'tasa' => $tasa,
            'aplica' => $retenido > 0,
            'base_calculo' => $base_retencion,
            'tipo_retencion' => 'ISR_PERSONA_JURIDICA',
            'base_legal' => 'Art. 228-AP',
        ];
    }

    /**
     * Obtiene la tasa ISR según tipo de pago.
     */
    private static function getTasaIsrPorTipoPago(string $tipo_pago, bool $es_juridica): float
    {
        switch (strtolower($tipo_pago)) {
            case self::TIPO_PAGO_HONORARIOS:
                return self::ISR_HONORARIOS_PROFESSIONALES;
            case self::TIPO_PAGO_ARRENDAMIENTO:
                return self::ISR_ARRENDAMIENTOS;
            case self::TIPO_PAGO_COMISION:
                return self::ISR_COMISIONES;
            case self::TIPO_PAGO_DIVIDENDOS:
                return self::ISR_DIVIDIDOS_UTILIDADES;
            case self::TIPO_PAGO_EXTRANJERO:
                return self::ISR_PAGOS_EXTRANJERO;
            case self::TIPO_PAGO_COMPRAS:
                return $es_juridica ? self::ISR_COMPRAS_NO_RESPONSABLE : self::ISR_COMPRAS_NO_RESPONSABLE;
            default:
                return self::ISR_SERVICIOS_GENERALES;
        }
    }

    /**
     * Determina si un proveedor/cliente está sujeto a retención ITBIS.
     * 
     * @param string|null $rnc RNC o cédula del contribuyente
     * @param string|null $tipo_persona 'juridica' o 'fisica'
     * @param string|null $regimen '10-10' (especial), '14-14' (ordinario), '25-10' (micro)
     * @return bool True si está sujeto a retención
     */
    public static function proveedorSujetoARetencionItbis(
        ?string $rnc,
        ?string $tipo_persona = 'juridica',
        ?string $regimen = null
    ): bool {
        if (empty($rnc)) {
            return false;
        }

        $rnc_limpio = preg_replace('/[^0-9]/', '', $rnc);
        $longitud = strlen($rnc_limpio);

        // Validar longitud mínima
        if ($longitud < 9) {
            return false;
        }

        // Contribuyentes Especiales (10-10) siempre están sujetos
        if ($regimen === '10-10') {
            return true;
        }

        // Contribuyentes Ordinarios (14-14) generalmente están sujetos
        if ($regimen === '14-14') {
            return true;
        }

        // Microcontribuyentes (25-10) NO están sujetos a retención
        if ($regimen === '25-10') {
            return false;
        }

        // Si no se conoce el régimen, verificar validez del RNC
        return true;
    }

    /**
     * Clasifica el tipo de documento según RNC/Cédula.
     * 
     * @param string $rnc Número de RNC o cédula
     * @return string 'CEDULA', 'RNC', 'EXTRANJERO', 'OTRO'
     */
    public static function clasificarTipoDocumento(string $rnc): string
    {
        $rnc_limpio = preg_replace('/[^0-9]/', '', $rnc);
        $longitud = strlen($rnc_limpio);

        if ($longitud === 9) {
            return 'CEDULA';
        }

        if ($longitud === 11) {
            $primerDigito = $rnc_limpio[0];
            // Cédula dominicana: empieza con 0, 1, 2, 3
            if (in_array($primerDigito, ['0', '1', '2', '3'], true)) {
                return 'CEDULA';
            }
            // RNC: empieza con 1, 4, 5
            if (in_array($primerDigito, ['1', '4', '5'], true)) {
                return 'RNC';
            }
            // Extranjero
            return 'EXTRANJERO';
        }

        return 'OTRO';
    }

    /**
     * Valida RNC/Cédula según algoritmo DGII.
     */
    public static function validarRncCedula(string $rnc, string $tipo = 'RNC'): bool
    {
        return RncValidator::validar($rnc, $tipo);
    }

    /**
     * Genera el código de comprobante de retención.
     * Formato: YYYYMMDD-NNNN donde NNNN es secuencial.
     */
    public static function generarCodigoComprobante(\DateTimeInterface $fecha, int $secuencial): string
    {
        return $fecha->format('Ymd') . '-' . str_pad($secuencial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtiene todas las tasas ISR vigentes para referencia.
     */
    public static function getTasasIsrReferencia(): array
    {
        return [
            'honorarios_profesionales' => [
                'tasa' => self::ISR_HONORARIOS_PROFESSIONALES,
                'base_legal' => 'Art. 228-AP',
                'descripcion' => 'Honorarios profesionales',
            ],
            'arrendamientos' => [
                'tasa' => self::ISR_ARRENDAMIENTOS,
                'base_legal' => 'Art. 228-AP',
                'descripcion' => 'Arrendamientos',
            ],
            'comisiones' => [
                'tasa' => self::ISR_COMISIONES,
                'base_legal' => 'Art. 228-AP',
                'descripcion' => 'Comisiones',
            ],
            'servicios_generales' => [
                'tasa' => self::ISR_SERVICIOS_GENERALES,
                'base_legal' => 'Art. 228-AP',
                'descripcion' => 'Servicios generales',
            ],
            'compras_no_responsable' => [
                'tasa' => self::ISR_COMPRAS_NO_RESPONSABLE,
                'base_legal' => 'Art. 228-AQ',
                'descripcion' => 'Compras a no responsables',
            ],
            'dividendos_utilidades' => [
                'tasa' => self::ISR_DIVIDIDOS_UTILIDADES,
                'base_legal' => 'Art. 228-AR',
                'descripcion' => 'Dividendos y utilidades',
            ],
            'pagos_al_exterior' => [
                'tasa' => self::ISR_PAGOS_EXTRANJERO,
                'base_legal' => 'Art. 228-AT',
                'descripcion' => 'Pagos al exterior',
            ],
        ];
    }
}
