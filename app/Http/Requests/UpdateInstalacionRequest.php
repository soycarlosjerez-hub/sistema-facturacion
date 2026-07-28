<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstalacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|exists:clientes,id',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'instalador_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:pendiente,programada,en_progreso,completada,cancelada',
            'direccion_instalacion' => 'nullable|string|max:300',
            'tipo_inmueble' => 'required|in:casa,apartamento,local,industrial',
            'programada_para' => 'nullable|date',
            'completada_en' => 'nullable|date|after_or_equal:programada_para',
            'nota_interna' => 'nullable|string|max:2000',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'exists:productos,id',
            'productos.*.cantidad' => 'integer|min:1',
            'productos.*.precio_unitario' => 'numeric|min:0',
        ];
    }
}
