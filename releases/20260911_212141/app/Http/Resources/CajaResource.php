<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CajaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'nombre' => $this->nombre,
            'codigo' => $this->codigoCorto,
            'sucursal_id' => $this->sucursal_id,
            'ubicacion' => $this->ubicacion,
            'estado' => $this->estado,
            'activo' => $this->activo,
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'sesion_activa' => new SesionCajaResource($this->whenLoaded('sesionActiva')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
