<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenDetalleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'producto_id'    => $this->producto_id,
            'producto'       => $this->whenLoaded('producto', fn() => [
                'id'    => $this->producto->id,
                'nombre' => $this->producto->nombre,
                'precio' => $this->producto->precio,
            ]),
            'cantidad'       => $this->cantidad,
            'precio_unitario'=> $this->precio_unitario,
            'subtotal'       => $this->subtotal,
            'notas'          => $this->notas,
            'curso'          => $this->curso,
            'estado_cocina'  => $this->estado_cocina,
            'cocina_updated_at' => $this->cocina_updated_at instanceof \Carbon\Carbon ? $this->cocina_updated_at->toIso8601String() : $this->cocina_updated_at,
        ];
    }
}
