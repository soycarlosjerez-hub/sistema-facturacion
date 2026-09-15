<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlantillaImpresionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $plantilla = $this->route('plantilla');
        $id = $plantilla instanceof \App\Models\PlantillaImpresion ? $plantilla->id : null;

        return [
            'codigo' => 'nullable|string|max:100|unique:plantilla_impresiones,codigo'.($id ? ",{$id}" : ''),
            'nombre' => 'required|string|max:200',
            'modulo' => 'required|string|in:ventas,historial_ventas,compras,cotizaciones,devoluciones,ordenes,conduces,presupuestos',
            'tipo_formato' => 'nullable|string|in:ticket,pdf',
            'formato_papel' => 'nullable|string|in:a4,letter,ticket_80,ticket_58',
            'orientation' => 'nullable|string|in:portrait,landscape',
            'modulo_target' => 'nullable|string|in:ventas,compras,cotizaciones,devoluciones,ordenes,conduces,presupuestos',
            'tipo_doc_target' => 'nullable|integer|in:0,1,2',
            'es_default' => 'nullable|boolean',
            'color_primario' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_secundario' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'logo_path' => 'nullable|string|max:500',
            'encabezado_texto' => 'nullable|string|max:500',
            'pie_pagina_texto' => 'nullable|string|max:500',
            'mostrar_logo' => 'boolean',
            'mostrar_encabezado' => 'boolean',
            'mostrar_pie' => 'boolean',
            'mostrar_datos_cliente' => 'boolean',
            'mostrar_datos_fiscales' => 'boolean',
            'mostrar_columna_cantidad' => 'boolean',
            'mostrar_columna_precio' => 'boolean',
            'mostrar_columna_subtotal' => 'boolean',
            'mostrar_columna_itbis' => 'boolean',
            'mostrar_garantias' => 'boolean',
            'mostrar_columna_ncf' => 'boolean',
            'mostrar_columna_cajero' => 'boolean',
            'mostrar_columna_sucursal' => 'boolean',
            'mostrar_pagos' => 'boolean',
            'mostrar_notas' => 'boolean',
            'orden_campos' => 'nullable|json',
            'sucursal_ids' => 'nullable|array',
            'sucursal_ids.*' => 'integer|exists:sucursales,id',
        ];
    }

    public function messages(): array
    {
        return [
            'modulo.in' => 'El módulo seleccionado no es válido.',
            'formato_papel.in' => 'El formato de papel seleccionado no es válido.',
            'color_primario.regex' => 'El color primario debe estar en formato hexadecimal (ej: #1a1a2e).',
            'color_secundario.regex' => 'El color secundario debe estar en formato hexadecimal (ej: #16213e).',
        ];
    }
}
