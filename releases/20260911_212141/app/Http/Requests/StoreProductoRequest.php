<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_servicio' => 'required|in:producto,servicio,general',
            'categoria_id' => 'nullable|exists:categorias,id',
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras',
            'codigo_referencia' => 'nullable|string|max:100|unique:productos,codigo_referencia',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'precio_compra' => 'nullable|numeric|min:0',
            'unidad_medida' => 'nullable|string|max:50',
            'itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'activo' => 'boolean',
            'incluir_kds' => 'boolean',
            'serial_imei' => 'nullable|string|max:100|unique:productos,serial_imei',
            'requiere_serial' => 'boolean',
            'vendible_imei' => 'boolean',
            'requiere_imei' => 'boolean',
            'es_licencia' => 'boolean',
            'tipo_licencia' => 'nullable|string|max:50',
            'licencia_max_usuarios' => 'nullable|integer|min:1',
            'requires_setup' => 'boolean',
            'especializacion' => 'nullable|string|max:100',
            'almacenamiento_gb' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'precio_servicio' => 'nullable|numeric|min:0',
            'duracion_servicio_horas' => 'nullable|integer|min:0',
            'garantia_dias' => 'nullable|integer|min:0',
            'garantia_terminos' => 'nullable|string|max:5000',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:200',
            'marca_tecnologica_id' => 'nullable|exists:marca_tecnologicas,id',
            'tipo_equipo' => 'nullable|string|max:50',
            'capacidad_btu' => 'nullable|integer|min:0',
            'capacidad_toneladas' => 'nullable|numeric|min:0',
            'eficiencia_seer' => 'nullable|numeric|min:0',
            'gas_refrigerante' => 'nullable|string|max:20',
            'voltaje' => 'nullable|string|max:20',
            'peso_kg' => 'nullable|numeric|min:0',
            'dimensiones' => 'nullable|string|max:100',
            'categoria_clima' => 'nullable|string|max:50',
            'categoria_tecnica' => 'nullable|string|max:100',
            'linea_negocio' => 'nullable|string|max:50',
            'product_type' => 'nullable|string|max:50',
            'is_art_piece' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.min' => 'El precio no puede ser negativo.',
            'codigo_barras.unique' => 'Este código de barras ya está registrado.',
            'imagen.max' => 'La imagen no debe superar 10MB.',
            'tipo_servicio.required' => 'Debes seleccionar un tipo de producto.',
            'tipo_servicio.in' => 'El tipo de producto seleccionado no es válido.',
        ];
    }
}
