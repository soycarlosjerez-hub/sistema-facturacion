<?php

namespace App\Support;

class RncValidator
{
    /**
     * Valida RNC dominicano con el algoritmo oficial DGII (módulo 11).
     * - Personas jurídicas: 11 dígitos (1er dígito 1 o 4)
     * - Cédulas: 11 dígitos (1er dígito 0, 1, 2, 3)
     * - Pasaporte: cualquier longitud (no validable)
     */
    public static function validar(string $rnc, ?string $tipoDocumento = null): bool
    {
        $rnc = preg_replace('/[^0-9]/', '', $rnc);

        if ($tipoDocumento === 'pasaporte') {
            return !empty($rnc);
        }
        if ($tipoDocumento === 'cedula') {
            if (strlen($rnc) !== 11) return false;
            if (!in_array($rnc[0], ['0', '1', '2', '3'], true)) return false;
            return self::checkDigitCedula($rnc);
        }

        // RNC: 9 dígitos sin guión o 11 con ceros a la izquierda (DGII almacena 9)
        // La DGII espera RNC de 9 dígitos para verificación
        $digits = $rnc;

        if (strlen($digits) === 11) {
            $digits = ltrim($digits, '0');
        }

        if (strlen($digits) !== 9) return false;
        if (!in_array($digits[0], ['1', '4', '5'], true)) return false;

        return self::checkDigitRnc($digits);
    }

    /**
     * Algoritmo oficial DGII para RNC (módulo 11)
     * Dígito verificador está en la última posición
     */
    public static function checkDigitRnc(string $rnc): bool
    {
        if (strlen($rnc) !== 9) return false;

        $digits = str_split(substr($rnc, 0, 8));
        $checkDigit = (int) $rnc[8];

        $weights = [7, 9, 8, 6, 5, 4, 3, 2];
        $sum = 0;

        foreach ($digits as $i => $digit) {
            $sum += (int) $digit * $weights[$i];
        }

        $remainder = $sum % 11;
        $calculated = $remainder === 0 ? 0 : (11 - $remainder);

        return $calculated === $checkDigit;
    }

    /**
     * Algoritmo de cédula dominicana (módulo 10)
     */
    public static function checkDigitCedula(string $cedula): bool
    {
        if (strlen($cedula) !== 11) return false;

        $digits = str_split(substr($cedula, 0, 10));
        $checkDigit = (int) $cedula[10];
        $weights = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
        $sum = 0;

        foreach ($digits as $i => $digit) {
            $product = (int) $digit * $weights[$i];
            $sum += $product > 9 ? $product - 9 : $product;
        }

        $remainder = ($sum * 9) % 10;
        return $remainder === $checkDigit;
    }

    /**
     * Devuelve el tipo de documento según el formato
     */
    public static function inferirTipo(string $numero): string
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);
        $len = strlen($numero);

        if ($len === 9 || ($len === 11 && in_array($numero[0], ['1', '4', '5'], true))) {
            return 'rnc';
        }
        if ($len === 11 && in_array($numero[0], ['0', '1', '2', '3'], true)) {
            return 'cedula';
        }
        return 'pasaporte';
    }

    /**
     * Formatea RNC a 9 dígitos (sin ceros a la izquierda) o DD-MM-AAAAXX para cédula
     */
    public static function formato(string $numero, string $tipo): string
    {
        $numero = preg_replace('/[^0-9]/', '', $numero);

        return match ($tipo) {
            'rnc' => str_pad($numero, 9, '0', STR_PAD_LEFT),
            'cedula' => str_pad(substr($numero, -11), 11, '0', STR_PAD_LEFT),
            default => $numero,
        };
    }

    /**
     * Clasifica documento para DGII (1=RNC, 2=Cédula, 3=Otro)
     */
    public static function tipoDocumentoDgii(?string $tipoDocumento): string
    {
        return match ($tipoDocumento) {
            'rnc' => '1',
            'cedula' => '2',
            default => '3',
        };
    }
}
