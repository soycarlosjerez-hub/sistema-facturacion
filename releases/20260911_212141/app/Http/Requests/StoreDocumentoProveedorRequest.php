<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoProveedorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proveedor_id' => 'required|exists:proveedores,id',
            'documento_sgc_id' => 'nullable|exists:documentos_sgc,id',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,zip,rar|max:20480',
            'descripcionDocumento' => 'nullable|string|max:500',
            'fechaCarga' => 'required|date',
            'fechaVencimiento' => 'nullable|date|after_or_equal:fechaCarga',
            'estado' => 'required|in:vigente,vencido,por_cargar,verificado',
        ];
    }

    public function messages(): array
    {
        return [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'archivo.required' => 'Debe adjuntar un archivo.',
            'archivo.max' => 'El archivo no debe superar los 20 MB.',
            'fechaVencimiento.after_or_equal' => 'La fecha de vencimiento debe ser igual o posterior a la fecha de carga.',
        ];
    }
}
