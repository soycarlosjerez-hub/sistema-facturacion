<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'  => 'nullable|integer',
            'sucursal_id' => 'required|integer',
            'tipo_orden'  => 'required|in:comida,bebida,mixto',
            'nota'        => 'nullable|string|max:500',
            'propina'     => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'sucursal_id.required' => 'Selecciona una sucursal.',
            'tipo_orden.required'  => 'El tipo de orden es obligatorio.',
            'tipo_orden.in'        => 'El tipo de orden debe ser: Comida, Bebida o Mixto.',
            'propina.min'          => 'La propina no puede ser negativa.',
        ];
    }
}
