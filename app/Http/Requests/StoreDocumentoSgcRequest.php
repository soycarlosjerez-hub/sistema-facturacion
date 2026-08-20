<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoSgcRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'nullable|string|max:30|unique:documentos_sgc,codigo',
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'categoria' => 'required|in:politica,trabajo_instructivo,procedimiento,formulario,registro,matriz,reporte,otro',
            'formato' => 'required|in:pdf,word,excel,imagen,texto',
            'version' => 'required|string|regex:/^\d+(\.\d+)*$/',
            'fecha_emision' => 'nullable|date',
            'fecha_revision' => 'nullable|date|after_or_equal:fecha_emision',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_revision|after_or_equal:fecha_emision',
            'estado' => 'required|in:borrador,revision,aprobado,vigente,obsoleto,archivado',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,gif,txt,csv|max:20480',
            'aprobado_por' => 'nullable|exists:users,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'observaciones' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del documento es obligatorio.',
            'categoria.required' => 'Debe seleccionar una categoría de documento.',
            'categoria.in' => 'La categoría seleccionada no es válida.',
            'formato.required' => 'Debe indicar el formato del documento.',
            'version.required' => 'Debe indicar la versión del documento.',
            'version.regex' => 'El formato de versión debe ser numérico (ej: 1.0, 2.1.3).',
            'estado.required' => 'Debe seleccionar un estado para el documento.',
            'fecha_revision.after_or_equal' => 'La fecha de revisión debe ser igual o posterior a la fecha de emisión.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de emisión y revisión.',
            'archivo.mimes' => 'El archivo debe ser: :values.',
            'archivo.max' => 'El archivo no debe superar los 20 MB.',
        ];
    }
}
