<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListaPrecioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'porcentaje' => $this->porcentaje,
            'sucursal_id' => $this->sucursal_id,
            'activa' => $this->activa,
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'productos' => ProductoResource::collection($this->whenLoaded('productos')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
