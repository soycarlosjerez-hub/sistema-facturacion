<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImpresoraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
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
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la impresora es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 100 caracteres.',
            'tipo_conexion.required' => 'Debes seleccionar un tipo de conexión.',
            'tipo_conexion.in' => 'El tipo de conexión debe ser: local, USB, red o PDF.',
            'papel_tamano.required' => 'El tamaño de papel es obligatorio.',
            'papel_tamano.in' => 'El tamaño de papel debe ser: 58mm, 80mm o A4.',
            'puerto.min' => 'El puerto debe ser un número válido (1-65535).',
            'direccion_ip.max' => 'La dirección IP no puede superar los 45 caracteres.',
        ];
    }
}
