<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServicioDomoticaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'tipo_servicio' => 'required|in:camaras_seguridad,alarmas,control_acceso,redes,automatizacion,sonido,iluminacion,otro',
            'direccion_instalacion' => 'nullable|string|max:500',
            'tecnico_id' => 'nullable|exists:tecnicos,id',
            'presupuesto' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'fecha_programada' => 'nullable|date',
            'notas' => 'nullable|string|max:2000',
        ];
    }
}
