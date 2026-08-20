<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMarcaTecnologicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'         => 'required|string|max:200|unique:marca_tecnologicas,nombre,' . $this->marcaTecnologica->id,
            'logo_url'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'website'        => 'nullable|url|max:255',
            'pais'           => 'nullable|string|max:100',
            'contacto_email' => 'nullable|email|max:255',
            'orden'          => 'nullable|integer|min:0',
            'activo'         => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'    => 'El nombre de la marca es obligatorio.',
            'nombre.unique'      => 'Ya existe otra marca con ese nombre.',
            'website.url'        => 'Ingresa una URL válida.',
            'contacto_email.email' => 'Ingresa un correo electrónico válido.',
            'logo_url.max'       => 'La imagen no debe superar 10MB.',
        ];
    }
}
