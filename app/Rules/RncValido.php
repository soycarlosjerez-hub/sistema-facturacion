<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Support\RncValidator;

class RncValido implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $clean = preg_replace('/[^0-9]/', '', $value);
        $tipo = strlen($clean) === 11 ? 'rnc' : 'cedula';

        // Permitir RNC de prueba solo en entornos no productivos
        if (!app()->isProduction() && $tipo === 'rnc' && $clean === '03104422229') {
            return;
        }

        if (!RncValidator::validar($clean, $tipo)) {
            $fail("El {$tipo} ingresado no es válido.");
        }
    }
}
