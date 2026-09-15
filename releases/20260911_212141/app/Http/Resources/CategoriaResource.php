<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
            'productos_count' => $this->whenLoaded('productos', fn () => $this->productos->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->when($this->deleted_at, fn () => $this->deleted_at->toISOString()),
        ];
    }
}
