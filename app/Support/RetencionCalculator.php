<?php

namespace App\Support;

class RetencionCalculator
{
    const ITBIS_TASA = 18;

    const ITBIS_RETENCION_TASA = 0.18; // 18% del ITBIS facturado (equivalente a 100% del ITBIS)
    const ITBIS_RETENCION_PORC = 1.0; // 100% del ITBIS

    const ISR_TASA_GENERAL = 0.10; // 10% para personas jurídicas
    const ISR_TASA_PERSONA_FISICA = 0.10; // 10% para personas físicas

    // Montos mínimos para retención (RD$) - actualizados 2026
    const ISR_MONTO_MINIMO_PERSONA_FISICA = 50000;
    const ISR_MONTO_MINIMO_PERSONA_JURIDICA = 0; // Desde el primer peso para jurídicas con RNC
    const ITBIS_MONTO_MINIMO = 0;

    /**
     * Calcula la retención de ITBIS según normativa DGII.
     * Se retiene el 100% del ITBIS facturado cuando el proveedor
     * está registrado en una categoría sujeta a retención.
     */
    public static function calcularRetencionItbis(float $itbisFacturado, bool $sujetoARetencion = true): array
    {
        if (!$sujetoARetencion || $itbisFacturado <= self::ITBIS_MONTO_MINIMO) {
            return [
                'monto_retenido' => 0,
                'tasa' => 0,
                'itbis_neto' => $itbisFacturado,
                'aplica' => false,
            ];
        }

        $retenido = round($itbisFacturado * self::ITBIS_RETENCION_PORC, 2);

        return [
            'monto_retenido' => $retenido,
            'tasa' => self::ITBIS_RETENCION_TASA,
            'itbis_neto' => round($itbisFacturado - $retenido, 2),
            'aplica' => true,
        ];
    }

    /**
     * Calcula la retención de ISR según normativa DGII.
     * Personas jurídicas: 10% desde el primer peso
     * Personas físicas: 10% sobre pagos > RD$50,000 mensuales
     */
    public static function calcularRetencionIsr(
        float $montoTotal,
        string $tipoProveedor = 'juridica',
        float $montoAcumuladoMes = 0
    ): array {
        $esJuridica = $tipoProveedor === 'juridica';

        if (!$esJuridica) {
            $montoTotalPeriodo = $montoTotal + $montoAcumuladoMes;
            if ($montoTotalPeriodo <= self::ISR_MONTO_MINIMO_PERSONA_FISICA) {
                return [
                    'monto_retenido' => 0,
                    'tasa' => 0,
                    'aplica' => false,
                    'excede_monto' => false,
                ];
            }
            $baseRetencion = max(0, $montoTotalPeriodo - self::ISR_MONTO_MINIMO_PERSONA_FISICA);
            $retenido = round($baseRetencion * self::ISR_TASA_PERSONA_FISICA, 2);
        } else {
            $baseRetencion = $montoTotal;
            $retenido = round($baseRetencion * self::ISR_TASA_GENERAL, 2);
        }

        return [
            'monto_retenido' => $retenido,
            'tasa' => $esJuridica ? self::ISR_TASA_GENERAL : self::ISR_TASA_PERSONA_FISICA,
            'aplica' => $retenido > 0,
            'excede_monto' => !$esJuridica && ($montoTotal + $montoAcumuladoMes) > self::ISR_MONTO_MINIMO_PERSONA_FISICA,
            'base_calculo' => $baseRetencion ?? $montoTotal,
        ];
    }

    /**
     * Determina si un proveedor está sujeto a retención ITBIS
     * según su régimen fiscal y tipo de persona
     */
    public static function proveedorSujetoARetencionItbis(?string $rnc, ?string $tipoProveedor = 'juridica'): bool
    {
        if (empty($rnc)) return false;
        $rnc = preg_replace('/[^0-9]/', '', $rnc);
        $len = strlen($rnc);
        if ($len < 9) return false;
        return true;
    }

    /**
     * Clasifica un RNC para reporte DGII
     */
    public static function clasificarTipoDocumento(string $rnc): string
    {
        $rnc = preg_replace('/[^0-9]/', '', $rnc);
        $len = strlen($rnc);
        if ($len === 9 || ($len === 11 && in_array($rnc[0], ['1', '4', '5'], true))) return 'RNC';
        if ($len === 11 && in_array($rnc[0], ['0', '1', '2', '3'], true)) return 'CEDULA';
        return 'OTRO';
    }
}
