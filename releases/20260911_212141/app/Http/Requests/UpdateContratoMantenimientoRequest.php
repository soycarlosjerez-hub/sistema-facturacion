<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContratoMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'tipo_periodicidad' => 'required|in:mensual,trimestral,semestral,aunal',
            'equipos_cubiertos' => 'nullable|array',
            'vigencia_desde' => 'required|date',
            'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
            'valor_mensual' => 'required|numeric|min:0',
            'incluye_visitas' => 'boolean',
            'num_visitas_anuales' => 'nullable|integer|min:0',
            'deducible' => 'nullable|numeric|min:0',
            'cobertura_maxima' => 'nullable|numeric|min:0',
        ];
    }
}
