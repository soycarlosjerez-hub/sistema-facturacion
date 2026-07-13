<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can('ventas.create');
    }

    public function rules(): array
    {
        return [
            'cliente_id'    => [
                Rule::requiredIf(fn () => in_array($this->metodo_pago, ['fiado', 'cuenta_abierta'])),
                'exists:clientes,id',
            ],
            'tipo_venta_id' => 'required|exists:tipos_ventas,id',
            'producto_id'   => 'required|array|min:1',
            'producto_id.*' => 'exists:productos,id',
            'almacen_id'    => 'nullable|array',
            'almacen_id.*'  => 'nullable|exists:almacenes,id',
            'cantidad'      => 'required|array|min:1',
            'cantidad.*'    => 'integer|min:1',
            'precio'        => 'required|array|min:1',
            'precio.*'      => 'numeric|min:0',
            'subtotal'      => 'required|array|min:1',
            'subtotal.*'    => 'numeric|min:0',
            'descuento'     => 'nullable|array',
            'descuento.*'   => 'numeric|min:0',
            'descuento_tipo' => 'nullable|array',
            'descuento_tipo.*' => 'in:monto,porcentaje',
            'total'         => 'required|numeric|min:0',
            'impuestos'     => 'nullable|numeric|min:0',
            'subtotal_final' => 'nullable|numeric|min:0',
            'propina'       => 'nullable|numeric|min:0',
            'general_descuento' => 'nullable|numeric|min:0',
            'metodo_pago'   => 'nullable|string|in:efectivo,tarjeta,transferencia,fiado,cuenta_abierta,mixto',
            'ncf_tipo'      => 'nullable|string|exists:ncf_sequences,tipo_comprobante',
            'tipo_comprobante' => 'nullable|in:sin,ncf,ecf',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Selecciona un cliente.',
            'producto_id.required' => 'Agrega al menos un producto.',
            'producto_id.min' => 'Agrega al menos un producto.',
            'cantidad.*.min' => 'La cantidad debe ser mayor a 0.',
            'precio.*.min' => 'El precio no puede ser negativo.',
            'total.min' => 'El total debe ser mayor a 0.',
        ];
    }
}
