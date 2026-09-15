<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotizacionItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cotizacion_id' => $this->cotizacion_id,
            'producto_id' => $this->producto_id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'unidad' => $this->unidad,
            'cantidad' => $this->cantidad,
            'precio_unitario' => $this->precio_unitario,
            'descuento' => $this->descuento,
            'itbis_porcentaje' => $this->itbis_porcentaje,
            'itbis' => $this->itbis,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'orden' => $this->orden,
            'producto' => new ProductoResource($this->whenLoaded('producto')),
        ];
    }
}
