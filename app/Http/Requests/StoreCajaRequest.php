<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCajaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'monto_inicial' => 'required|numeric|min:0',
            'notas'         => 'nullable|string|max:500',
            'caja_id'       => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'monto_inicial.required' => 'El monto inicial es obligatorio.',
            'monto_inicial.numeric'  => 'El monto inicial debe ser un número válido.',
            'monto_inicial.min'      => 'El monto inicial no puede ser negativo.',
            'caja_id.required'       => 'Selecciona una caja.',
        ];
    }
}
