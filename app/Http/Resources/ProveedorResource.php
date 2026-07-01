<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProveedorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'rnc' => $this->rnc,
            'tipo_persona' => $this->tipo_persona,
            'sujeto_retencion_isr' => $this->sujeto_retencion_isr,
            'sujeto_retencion_itbis' => $this->sujeto_retencion_itbis,
            'tenant_id' => $this->tenant_id,
            'compras_count' => $this->whenLoaded('compras', fn () => $this->compras->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
