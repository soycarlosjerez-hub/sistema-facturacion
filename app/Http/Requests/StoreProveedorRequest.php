<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'               => 'nullable|string|max:255',
            'email'                => 'nullable|email|max:255',
            'telefono'             => 'nullable|string|max:50',
            'rnc'                  => 'nullable|string|max:20',
            'tipo_persona'         => 'nullable|in:fisica,juridica',
            'sujeto_retencion_isr' => 'boolean',
            'sujeto_retencion_itbis' => 'boolean',
            'activo'               => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.string'              => 'El nombre debe ser un texto.',
            'telefono.string'            => 'El teléfono debe ser un texto.',
            'tipo_persona.in'            => 'El tipo de persona debe ser Física o Jurídica.',
            'sujeto_retencion_isr.boolean' => 'El valor de sujeto retención ISR no es válido.',
            'sujeto_retencion_itbis.boolean' => 'El valor de sujeto retención ITBIS no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sujeto_retencion_isr'   => $this->boolean('sujeto_retencion_isr'),
            'sujeto_retencion_itbis' => $this->boolean('sujeto_retencion_itbis'),
            'activo'                 => $this->boolean('activo'),
        ]);
    }
}
