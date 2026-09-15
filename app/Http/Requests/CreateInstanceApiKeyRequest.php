<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInstanceApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{L}\p{N}_\-.\s]+$/u',
                Rule::unique('instance_api_keys')->where(fn ($q) => $q->where('business_instance_id', $this->route('instance')?->id)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la API Key es obligatorio.',
            'name.regex' => 'El nombre solo puede contener letras, números, espacios, guiones, guiones bajos y puntos.',
            'name.unique' => 'Ya existe una API Key con ese nombre para esta instancia.',
        ];
    }
}
