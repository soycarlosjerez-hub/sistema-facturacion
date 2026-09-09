<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('categorias.edit', $this->route('category'));
    }

    public function rules(): array
    {
        $categoria = $this->route('category');
        
        return [
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('categorias')->where(fn ($q) => $q->where('tenant_id', $this->user()->business_instance_id))->ignore($categoria->id),
            ],
            'descripcion' => 'nullable|string|max:255',
            'activa' => 'boolean',
            'productos' => 'nullable|array',
            'productos.*' => 'integer|exists:productos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una categoría con este nombre en tu tenant.',
            'descripcion.max' => 'La descripción no puede exceder los 255 caracteres.',
            'productos.*.exists' => 'Uno o más productos seleccionados no son válidos.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'activa' => $this->boolean('activa'),
        ]);
    }
}
