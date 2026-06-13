<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id'           => 'sometimes|required|exists:proveedores,id',
            'almacen_id'             => 'nullable|exists:almacenes,id',
            'tipo_compra_id'         => 'sometimes|required|exists:tipos_compras,id',
            'fecha'                  => 'nullable|date',
            'observaciones'          => 'nullable|string|max:1000',
            'aplica_retencion_isr'   => 'nullable|boolean',
            'aplica_retencion_itbis' => 'nullable|boolean',
            'productos'              => 'nullable|array',
            'productos.*.producto_id'      => 'nullable|integer|exists:productos,id',
            'productos.*.nombre'           => 'required_with:productos|string|max:255',
            'productos.*.codigo_barras'    => 'nullable|string|max:100',
            'productos.*.cantidad'         => 'required_with:productos|numeric|min:0.01',
            'productos.*.precio'           => 'required_with:productos|numeric|min:0',
            'productos.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required'            => 'Selecciona un proveedor.',
            'tipo_compra_id.required'          => 'Selecciona un tipo de compra.',
            'productos.*.nombre.required_with' => 'El nombre del producto es obligatorio.',
            'productos.*.cantidad.required_with' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.min'         => 'La cantidad debe ser mayor a 0.',
            'productos.*.precio.required_with' => 'El precio es obligatorio.',
        ];
    }
}
