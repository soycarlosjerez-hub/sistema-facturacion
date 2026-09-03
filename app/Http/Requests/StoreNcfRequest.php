<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNcfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'prefijo'          => 'required|string|max:20',
            'tipo_ncf'         => 'required|in:B01,E01,E02,E03,E31,E32,E34,F01,X01',
            'secuencia_desde'  => 'required|integer|min:1',
            'secuencia_hasta'  => 'required|integer|gt:secuencia_desde',
            'fecha_vencimiento' => 'required|date',
            'activo'           => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'         => 'El nombre de la secuencia es obligatorio.',
            'prefijo.required'        => 'El prefijo es obligatorio.',
            'tipo_ncf.required'       => 'El tipo de NCF es obligatorio.',
            'tipo_ncf.in'             => 'El tipo de NCF seleccionado no es válido.',
            'secuencia_desde.required' => 'La secuencia de inicio es obligatoria.',
            'secuencia_desde.min'     => 'La secuencia debe ser al menos 1.',
            'secuencia_hasta.required' => 'La secuencia de fin es obligatoria.',
            'secuencia_hasta.gt'      => 'La secuencia de fin debe ser mayor que la secuencia de inicio.',
            'fecha_vencimiento.required' => 'La fecha de vencimiento es obligatoria.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activo' => $this->boolean('activo'),
        ]);
    }
}
