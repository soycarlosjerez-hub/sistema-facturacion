<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'tecnico_id' => 'nullable|exists:users,id',
            'tipo' => 'required|in:preventivo,correctivo',
            'estado' => 'required|in:pendiente,programada,en_curso,completado,cancelado',
            'contrato_mantenimiento_id' => 'nullable|exists:contratos_mantenimiento,id',
            'descripcion_falla' => 'nullable|string|max:2000',
            'solucion_aplicada' => 'nullable|string|max:2000',
            'repuestos_usados' => 'nullable|array',
            'repuestos_usados.*.nombre' => 'required_with:repuestos_usados|string|max:100',
            'repuestos_usados.*.cantidad' => 'nullable|integer|min:1',
            'repuestos_usados.*.precio' => 'nullable|numeric|min:0',
            'costo_repuestos' => 'nullable|numeric|min:0',
            'mano_de_obra' => 'nullable|numeric|min:0',
            'programada_para' => 'nullable|date',
            'completada_en' => 'nullable|date',
        ];
    }
}
