<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSucursalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:20|unique:sucursales,codigo,NULL,id,tenant_id,' . auth()->user()->business_instance_id,
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'rnc' => 'nullable|string|max:20',
            'activa' => 'nullable|boolean',
            'es_matriz' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código de la sucursal es obligatorio.',
            'codigo.unique' => 'Este código ya está en uso.',
            'nombre.required' => 'El nombre de la sucursal es obligatorio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activa' => $this->boolean('activa'),
            'es_matriz' => $this->boolean('es_matriz'),
        ]);
    }
}
