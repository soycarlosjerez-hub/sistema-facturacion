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
            'delivery_fee'  => 'nullable|numeric|min:0',
            'cargo_servicio'=> 'nullable|numeric|min:0',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $totals = $this->input('total', 0);
            $subtotals = $this->input('subtotal', []);
            $precios = $this->input('precio', []);
            $cantidades = $this->input('cantidad', []);

            if (is_array($subtotals) && count($subtotals) > 0) {
                $sumaSubtotales = array_sum(array_map('floatval', $subtotals));
                if (abs($sumaSubtotales - $totals) > 0.02) {
                    $validator->errors()->add('total', "El total ({$totals}) no coincide con la suma de subtotales ({$sumaSubtotales}). Verifique los cálculos.");
                }
            }

            if (is_array($precios) && is_array($cantidades)) {
                $maxItems = min(count($precios), count($cantidades));
                for ($i = 0; $i < $maxItems; $i++) {
                    $calc = (float)($precios[$i] ?? 0) * (float)($cantidades[$i] ?? 0);
                    $expectedSub = is_array($subtotals) ? (float)($subtotals[$i] ?? 0) : 0;
                    if (abs($calc - $expectedSub) > 0.02) {
                        $validator->errors()->add("subtotal.{$i}", "El subtotal no coincide con precio × cantidad.");
                    }
                }
            }
        });
    }
}
