<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlmacenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'nombre' => $this->nombre,
            'ubicacion' => $this->ubicacion,
            'sucursal_id' => $this->sucursal_id,
            'movimientos_count' => $this->whenLoaded('movimientos', fn () => $this->movimientos->count()),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
