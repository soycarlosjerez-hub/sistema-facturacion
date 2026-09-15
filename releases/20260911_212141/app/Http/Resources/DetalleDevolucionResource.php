<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetalleDevolucionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'devolucion_id' => $this->devolucion_id,
            'producto_id' => $this->producto_id,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'itbis_porcentaje' => $this->itbis_porcentaje,
            'subtotal' => $this->subtotal,
            'motivo' => $this->motivo,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
        ];
    }
}
