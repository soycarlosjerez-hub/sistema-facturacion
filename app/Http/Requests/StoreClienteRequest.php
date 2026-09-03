<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'            => 'required|string|max:255',
            'email'             => 'nullable|email|max:255',
            'telefono'          => 'required|string|max:50',
            'rnc_cedula'        => 'required|string|max:20',
            'direccion'         => 'required|string|max:500',
            'tipo_documento'    => 'required|in:rnc,cedula',
            'tipo_cliente'      => 'required|in:consumidor_final,empresa,profesional',
            'limite_credito'    => 'nullable|numeric|min:0',
            'plazo_pago_dias'   => 'nullable|integer|min:0',
            'activo'            => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'       => 'El nombre del cliente es obligatorio.',
            'email.email'           => 'Ingresa un correo electrónico válido.',
            'telefono.required'     => 'El teléfono es obligatorio.',
            'rnc_cedula.required'   => 'El RNC o Cédula es obligatorio.',
            'direccion.required'    => 'La dirección es obligatoria.',
            'tipo_documento.required' => 'El tipo de documento es obligatorio.',
            'tipo_documento.in'     => 'El tipo de documento debe ser RNC o Cédula.',
            'tipo_cliente.required' => 'El tipo de cliente es obligatorio.',
            'tipo_cliente.in'       => 'El tipo de cliente debe ser: Consumidor Final, Empresa o Profesional.',
            'limite_credito.min'    => 'El límite de crédito no puede ser negativo.',
            'plazo_pago_dias.min'   => 'El plazo de pago no puede ser negativo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activo' => $this->boolean('activo'),
        ]);
    }
}
