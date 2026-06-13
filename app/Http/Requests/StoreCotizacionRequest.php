<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_validez' => 'required|date|after_or_equal:fecha',
            'items' => 'required|array|min:1',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
            'items.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
            'descuento' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
            'condiciones' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Agrega al menos un producto.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha_validez.after_or_equal' => 'La fecha de validez debe ser posterior a la fecha de emisión.',
        ];
    }
}
