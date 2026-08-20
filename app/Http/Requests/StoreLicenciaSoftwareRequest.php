<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLicenciaSoftwareRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id'       => 'nullable|exists:productos,id',
            'clave_licencia'    => 'required|string|max:255',
            'tipo_licencia'     => 'nullable|string|max:100',
            'usuario_asignado'  => 'nullable|string|max:200',
            'licencia_activa'   => 'boolean',
            'fecha_vencimiento' => 'nullable|date',
            'plataforma'        => 'nullable|string|max:100',
            'notas'             => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'clave_licencia.required' => 'La clave de licencia es obligatoria.',
            'fecha_vencimiento.date'  => 'La fecha de vencimiento debe ser una fecha válida.',
        ];
    }
}
