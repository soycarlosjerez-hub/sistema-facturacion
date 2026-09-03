<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero'      => 'required|string|max:20',
            'nombre'      => 'nullable|string|max:100',
            'capacidad'   => 'required|integer|min:1',
            'estado'      => 'required|in:disponible,ocupada,reservada,mantenimiento',
            'categoria_id' => 'nullable|integer',
            'ubicacion_id' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'numero.required'    => 'El número de mesa es obligatorio.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.min'      => 'La capacidad debe ser al menos 1.',
            'estado.required'    => 'El estado de la mesa es obligatorio.',
            'estado.in'          => 'El estado debe ser: Disponible, Ocupada, Reservada o Mantenimiento.',
        ];
    }
}
