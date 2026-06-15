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
                Rule::unique('categories')->where(fn ($q) => $q->where('tenant_id', $this->user()->tenant_id)),
            ],
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'boolean',
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'icono' => 'nullable|string|max:50',
            'orden' => 'integer|min:0',
            'configuracion' => 'nullable|array',
            'type_keys' => 'required|array|min:1',
            'type_keys.*' => 'string|exists:business_types,key',
            'type_configs' => 'nullable|array',
            'type_configs.*' => 'array',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una categoría con este nombre en tu tenant.',
            'type_keys.required' => 'Debes seleccionar al menos un tipo de negocio.',
            'type_keys.*.exists' => 'El tipo de negocio seleccionado no existe.',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #3b82f6).',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activa' => $this->boolean('activa', true),
            'orden' => $this->integer('orden', 0),
        ]);
    }
}