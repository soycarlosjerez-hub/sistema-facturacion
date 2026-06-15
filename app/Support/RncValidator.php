<?php

namespace App\Support;

class RncValidator
{
    public static function validar($rnc, $tipo = 'cedula'): bool
    {
        $rnc = preg_replace('/[^0-9]/', '', $rnc);
        if (strlen($rnc) < 9) return false;

        return match (strlen($rnc)) {
            9  => self::validarCedula($rnc),
            11 => self::validarRNC($rnc),
            default => false,
        };
    }

    public static function inferirTipo($rnc): string
    {
        $rnc = preg_replace('/[^0-9]/', '', $rnc);
        return strlen($rnc) === 11 ? 'rnc' : 'cedula';
    }

    private static function validarCedula($cedula): bool
    {
        $multiplicadores = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
        $suma = 0;
        for ($i = 0; $i < 10; $i++) {
            $producto = (int)$cedula[$i] * $multiplicadores[$i];
            $suma += $producto >= 10 ? $producto - 9 : $producto;
        }
        $digito = (ceil($suma / 10) * 10) - $suma;
        return $digito === (int)$cedula[10];
    }

    private static function validarRNC($rnc): bool
    {
        $multiplicadores = [7, 9, 8, 6, 5, 4, 3, 2];
        $suma = 0;
        for ($i = 0; $i < 8; $i++) {
            $suma += (int)$rnc[$i] * $multiplicadores[$i];
        }
        $digito = 11 - ($suma % 11);
        $digito = $digito === 11 ? 0 : ($digito === 10 ? 2 : $digito);
        return $digito === (int)$rnc[8];
    }

    public static function tipoDocumentoDgii($tipo): string
    {
        return match (strtolower((string)$tipo)) {
            '1', 'rnc' => '1',
            '2', 'cedula', 'cédula' => '2',
            '3', 'pasaporte' => '3',
            default => '2',
        };
    }

    public static function formato(?string $rnc, $tipo = null): string
    {
        $clean = preg_replace('/[^0-9]/', '', $rnc ?? '');
        if ($clean === '') return '';

        $len = strlen($clean);

        if ($len === 9) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 5) . '-' . $clean[8];
        }

        if ($len === 11) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . $clean[10];
        }

        return $clean;
    }
}
