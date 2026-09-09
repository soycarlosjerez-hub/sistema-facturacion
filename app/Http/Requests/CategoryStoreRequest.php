<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('categorias.create');
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categorias')->where(fn ($q) => $q->where('tenant_id', $this->user()->business_instance_id)),
            ],
            'descripcion' => 'nullable|string|max:255',
            'activa' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una categoría con este nombre en tu tenant.',
            'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activa' => $this->boolean('activa', true),
        ]);
    }
}
