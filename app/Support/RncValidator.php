<?php

namespace App\Support;

/**
 * Validador de RNC y Cédula dominicanos (DGII).
 *
 * - RNC: 9 dígitos, formato 3-5-1 (ej. 001-00000-1).
 * - Cédula: 11 dígitos, formato 3-7-1 (ej. 001-0000000-1).
 */
class RncValidator
{
    public static function validar($rnc, $tipo = 'auto'): bool
    {
        $rnc = preg_replace('/[^0-9]/', '', (string) $rnc);

        $tipo = strtolower((string) $tipo);
        if (in_array($tipo, ['auto', 'automatico', ''], true)) {
            $tipo = self::inferirTipo($rnc);
        }

        if ($tipo === 'rnc') {
            return strlen($rnc) === 9 && self::validarRNC($rnc);
        }

        // cédula
        return strlen($rnc) === 11 && self::validarCedula($rnc);
    }

    public static function inferirTipo($rnc): string
    {
        $rnc = preg_replace('/[^0-9]/', '', (string) $rnc);
        $len = strlen($rnc);

        if ($len === 9) {
            return 'rnc';
        }

        if ($len === 11) {
            return 'cedula';
        }

        return $len > 10 ? 'cedula' : 'rnc';
    }

    /**
     * Cédula dominicana: 11 dígitos. Se ponderan los primeros 10 con
     * 1,2,1,2,...; si un producto >= 10 se suman sus dígitos (equivale a -9).
     * Dígito verificador = (10 - (suma % 10)) % 10, comparado con el 11.º.
     */
    private static function validarCedula($cedula): bool
    {
        if (strlen($cedula) !== 11 || ! ctype_digit($cedula)) {
            return false;
        }

        $suma = 0;
        for ($i = 0; $i < 10; $i++) {
            $producto = ((int) $cedula[$i]) * ($i % 2 === 0 ? 1 : 2);
            $suma += $producto >= 10 ? $producto - 9 : $producto;
        }
        $digitoVerificador = (10 - ($suma % 10)) % 10;

        return $digitoVerificador === (int) $cedula[10];
    }

    /**
     * RNC dominicano: 9 dígitos. Pesos 7,9,8,6,5,4,3,2 sobre los
     * primeros 8; dígito = 11 - (suma % 11); 11→0, 10→2 (norma DGII).
     */
    private static function validarRNC($rnc): bool
    {
        if (strlen($rnc) !== 9 || ! ctype_digit($rnc)) {
            return false;
        }

        $multiplicadores = [7, 9, 8, 6, 5, 4, 3, 2];
        $suma = 0;
        for ($i = 0; $i < 8; $i++) {
            $suma += (int) $rnc[$i] * $multiplicadores[$i];
        }
        $digito = 11 - ($suma % 11);
        $digito = $digito === 11 ? 0 : ($digito === 10 ? 2 : $digito);

        return $digito === (int) $rnc[8];
    }

    public static function tipoDocumentoDgii($tipo): string
    {
        return match (strtolower((string) $tipo)) {
            '1', 'rnc' => '1',
            '2', 'cedula', 'cédula' => '2',
            '3', 'pasaporte' => '3',
            default => '2',
        };
    }

    public static function formato(?string $rnc, $tipo = null): string
    {
        $clean = preg_replace('/[^0-9]/', '', $rnc ?? '');
        if ($clean === '') {
            return '';
        }

        $len = strlen($clean);

        if ($len === 9) {
            return substr($clean, 0, 3).'-'.substr($clean, 3, 5).'-'.$clean[8];
        }

        if ($len === 11) {
            return substr($clean, 0, 3).'-'.substr($clean, 3, 7).'-'.$clean[10];
        }

        return $clean;
    }
}
