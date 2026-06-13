<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productoId = $this->route('producto');
        return [
            'categoria_id'    => 'nullable|exists:categorias,id',
            'nombre'          => 'required|string|max:255',
            'codigo_barras'   => 'nullable|string|max:100|unique:productos,codigo_barras,' . $productoId,
            'descripcion'     => 'nullable|string|max:1000',
            'precio'          => 'required|numeric|min:0',
            'precio_compra'   => 'nullable|numeric|min:0',
            'unidad_medida'   => 'nullable|string|max:50',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'stock'           => 'nullable|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'imagen'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.min'      => 'El precio no puede ser negativo.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado.',
        ];
    }
}
