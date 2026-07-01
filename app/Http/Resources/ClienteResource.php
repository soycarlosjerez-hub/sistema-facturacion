<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'rnc_cedula' => $this->rnc_cedula,
            'rnc' => $this->rnc,
            'tipo_documento' => $this->tipo_documento,
            'tipo_cliente' => $this->tipo_cliente,
            'tipo_cliente_label' => $this->tipo_cliente_label,
            'color_badge' => $this->color_badge,
            'limite_credito' => (float) $this->limite_credito,
            'balance_pendiente' => (float) $this->balance_pendiente,
            'tenant_id' => $this->tenant_id,
            'ventas_count' => $this->whenLoaded('ventas', fn () => $this->ventas->count()),
            'cotizaciones_count' => $this->whenLoaded('cotizaciones', fn () => $this->cotizaciones->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
