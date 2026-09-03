<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CajaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'                    => 'required|string|max:255',
            'codigo'                    => 'required|string|max:50',
            'sucursal_id'               => 'required|integer',
            'ubicacion'                 => 'nullable|string|max:255',
            'allowed_comprobante_types' => 'required|array',
            'allowed_comprobante_types.*' => 'in:sin,ncf,ecf',
            'activo'                    => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'           => 'El nombre de la caja es obligatorio.',
            'codigo.required'           => 'El código de la caja es obligatorio.',
            'sucursal_id.required'      => 'Selecciona una sucursal.',
            'allowed_comprobante_types.required' => 'Selecciona al menos un tipo de comprobante permitido.',
            'allowed_comprobante_types.*' => 'El tipo de comprobante seleccionado no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activo' => $this->boolean('activo'),
        ]);
    }
}
