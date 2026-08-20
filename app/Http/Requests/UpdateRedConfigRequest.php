<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRedConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'    => 'nullable|exists:clientes,id',
            'nombre_red'    => 'required|string|max:200',
            'direccion_red' => 'nullable|string|max:50',
            'vlan_id'       => 'nullable|integer|min:1|max:4094',
            'ssid_wifi'     => 'nullable|string|max:100',
            'canal_wifi'    => 'nullable|integer|min:1|max:14',
            'cobertura'     => 'nullable|string|max:200',
            'dhcp_activado' => 'boolean',
            'dhcp_rango'    => 'nullable|array',
            'dhcp_rango.*'  => 'nullable|ip',
            'notas'         => 'nullable|string|max:2000',
            'activo'        => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_red.required' => 'El nombre de la red es obligatorio.',
            'dhcp_rango.*.ip'     => 'Cada rango DHCP debe ser una dirección IP válida.',
        ];
    }
}
