<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenEmergenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'prioridad' => 'required|in:critica,alta,media,baja',
            'tipo_falla' => 'required|in:sin_frio,sin_calor,fuga_gas,ruido_excesivo,cortocircuito,otro',
            'direccion' => 'nullable|string|max:300',
            'contacto_telefono' => 'nullable|string|max:30',
            'estado' => 'required|in:reportada,asignada,en_camino,en_lugar,resuelta,cerrada',
            'descripcion' => 'required|string|min:10',
            'tecnico_id' => 'nullable|exists:users,id',
            'costo_estimado' => 'nullable|numeric|min:0',
            'costo_final' => 'nullable|numeric|min:0',
            'respondida_en' => 'nullable|date',
            'resuelta_en' => 'nullable|date',
        ];
    }
}
