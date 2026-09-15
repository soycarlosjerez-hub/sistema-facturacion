<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImpresoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('impresora')?->id ?? $this->impresora;

        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('impresoras', 'nombre')->ignore($id, 'id')->where(function ($query) {
                return $query->where('tenant_id', auth()->user()->business_instance_id);
            })],
            'tipo' => 'nullable|string|max:50',
            'sucursal_id' => 'nullable|exists:sucursales,id',
            'tipo_conexion' => 'required|in:local,usb,red,pdf',
            'direccion_ip' => 'nullable|string|max:45',
            'puerto' => 'nullable|integer|min:1|max:65535',
            'ruta_compartida' => 'nullable|string|max:255',
            'driver' => 'nullable|string|max:50',
            'papel_tamano' => 'required|in:58mm,80mm,A4',
            'caracteres_por_linea' => 'nullable|integer|min:24|max:132',
            'auto_imprimir_ventas' => 'nullable|boolean',
            'auto_imprimir_cotizaciones' => 'nullable|boolean',
            'auto_imprimir_conduces' => 'nullable|boolean',
            'activo' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string|max:500',
            'configuracion' => 'nullable|array',
            'configuracion.copias' => 'nullable|integer|min:1|max:10',
            'configuracion.densidad' => 'nullable|in:normal,alta,baja',
            'configuracion.font_size' => 'nullable|in:9px,11px,12px,14px',
            'configuracion.impresion' => 'nullable|in:normal,compacto,espaciado',
            'configuracion.margenes.top' => 'nullable|integer|min:0|max:20',
            'configuracion.margenes.right' => 'nullable|integer|min:0|max:20',
            'configuracion.margenes.bottom' => 'nullable|integer|min:0|max:20',
            'configuracion.margenes.left' => 'nullable|integer|min:0|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe otra impresora con ese nombre en tu empresa.',
        ];
    }
}
