<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentoSgcRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $docId = $this->route('documento');
        return [
            'codigo' => Rule::unique('documentos_sgc', 'codigo')->ignore($docId),
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
            'codigo.unique' => 'Este código ya está en uso por otro documento.',
            'titulo.required' => 'El título del documento es obligatorio.',
            'version.regex' => 'El formato de versión debe ser numérico (ej: 1.0, 2.1.3).',
        ];
    }
}
