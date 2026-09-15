<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleCompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'compra_id' => $this->compra_id,
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'precio_unitario' => (float) $this->precio_unitario,
            'itbis_porcentaje' => (float) $this->itbis_porcentaje,
            'subtotal' => (float) $this->subtotal,
            'base' => $this->base,
            'itbis_monto' => $this->itbis_monto,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
