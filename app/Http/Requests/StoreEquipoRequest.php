<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id'   => 'required|integer',
            'serial_imei'   => 'required|string|unique:equipos,serial_imei',
            'marca'         => 'required|string|max:100',
            'modelo'        => 'required|string|max:100',
            'estado'        => 'required|in:disponible,vendido,reparacion,reservado',
            'garantia_desde' => 'nullable|date',
            'garantia_hasta' => 'nullable|date|after_or_equal:garantia_desde',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required'  => 'Selecciona un producto.',
            'serial_imei.required'  => 'El número de serie o IMEI es obligatorio.',
            'serial_imei.unique'    => 'Ya existe un equipo con ese número de serie o IMEI.',
            'marca.required'        => 'La marca es obligatoria.',
            'modelo.required'       => 'El modelo es obligatorio.',
            'estado.required'       => 'El estado del equipo es obligatorio.',
            'estado.in'             => 'El estado debe ser: Disponible, Vendido, Reparación o Reservado.',
            'garantia_hasta.after_or_equal' => 'La fecha de fin de garantía debe ser igual o posterior a la fecha de inicio.',
        ];
    }
}
