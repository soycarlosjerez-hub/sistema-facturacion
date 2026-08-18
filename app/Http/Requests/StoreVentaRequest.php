<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        if ($user->hasRole(['admin', 'owner', 'admin-business'])) {
            return true;
        }

        if (in_array($user->role, ['admin', 'owner', 'admin-business', 'root'])) {
            return true;
        }

        return $user->can('ventas.create');
    }

    public function rules(): array
    {
        $tieneObras = $this->has('obra_id') && is_array($this->input('obra_id')) && count(array_filter($this->input('obra_id'))) > 0;

        $reglas = [
            'cliente_id'    => [
                Rule::requiredIf(fn () => in_array($this->metodo_pago, ['fiado', 'cuenta_abierta'])),
                'exists:clientes,id',
            ],
            'tipo_venta_id' => 'required|exists:tipos_ventas,id',
            'producto_id'   => [$tieneObras ? 'nullable' : 'required', 'array', 'min:1'],
            'producto_id.*' => 'exists:productos,id',
            'obra_id'       => 'nullable|array',
            'obra_id.*'     => 'exists:arte_obras,id',
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
            'itbis_porcentaje' => 'nullable|array',
            'itbis_porcentaje.*' => 'numeric|min:0|max:100',
            'sin_itbis' => 'nullable|array',
            'sin_itbis.*' => 'boolean',
            'admin_token' => 'nullable|string',
            'total'         => 'required|numeric|min:0',
            'impuestos'     => 'nullable|numeric|min:0',
            'subtotal_final' => 'nullable|numeric|min:0',
            'propina'       => 'nullable|numeric|min:0',
            'delivery_fee'  => 'nullable|numeric|min:0',
            'cargo_servicio'=> 'nullable|numeric|min:0',
            'general_descuento' => 'nullable|numeric|min:0',
            'metodo_pago'   => 'nullable|string|in:efectivo,tarjeta,transferencia,fiado,cuenta_abierta,mixto',
            'mixto_efectivo' => 'nullable|numeric|min:0',
            'mixto_tarjeta'  => 'nullable|numeric|min:0',
            'mixto_transferencia' => 'nullable|numeric|min:0',
            'ncf_tipo'      => ['nullable', 'string', 'exists:ncf_sequences,prefijo', Rule::requiredIf(fn() => $this->tipo_comprobante === 'ncf')],
            'tipo_comprobante' => 'nullable|in:sin,ncf,ecf',
        ];

        return $reglas;
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
            $precios = $this->input('precio', []);
            $cantidades = $this->input('cantidad', []);
            $subtotals = $this->input('subtotal', []);

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
