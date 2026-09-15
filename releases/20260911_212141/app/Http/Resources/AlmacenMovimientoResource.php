<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlmacenMovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'producto_id' => $this->producto_id,
            'detalle_compra_id' => $this->detalle_compra_id,
            'user_id' => $this->user_id,
            'almacen_id' => $this->almacen_id,
            'tipo' => $this->tipo,
            'cantidad' => $this->cantidad,
            'nota' => $this->nota,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
            'user' => new UserResource($this->whenLoaded('user')),
            'almacen' => new AlmacenResource($this->whenLoaded('almacen')),
            'detalleCompra' => new DetalleCompraResource($this->whenLoaded('detalleCompra')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
