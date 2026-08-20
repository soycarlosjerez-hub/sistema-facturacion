<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePresupuestoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'estado' => 'nullable|in:borrador,enviado,aceptado,rechazado,cancelado',
            'valido_hasta' => 'nullable|date|after_or_equal:today',
            'notas' => 'nullable|string|max:2000',
            'items' => 'nullable|array',
            'items.*.producto_id' => 'nullable|exists:productos,id',
            'items.*.descripcion' => 'nullable|string|max:500',
            'items.*.cantidad' => 'required_if:items,.*|numeric|min:0.01',
            'items.*.precio_unitario' => 'required_if:items,.*|numeric|min:0',
            'items.*.tipo_item' => 'nullable|in:producto,mano_obra,desplazamiento,servicio,licencia,otro',
            'items.*.descuento' => 'nullable|numeric|min:0',
            'items.*.itbis_porcentaje' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio.',
            'items.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'items.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'valido_hasta.after_or_equal' => 'La fecha de validez debe ser posterior a hoy.',
        ];
    }
}
