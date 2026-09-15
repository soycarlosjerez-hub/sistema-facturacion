<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotizacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'cliente_id' => $this->cliente_id,
            'sucursal_id' => $this->sucursal_id,
            'user_id' => $this->user_id,
            'fecha' => $this->fecha,
            'fecha_validez' => $this->fecha_validez,
            'subtotal' => $this->subtotal,
            'descuento' => $this->descuento,
            'itbis' => $this->itbis,
            'total' => $this->total,
            'estado' => $this->estado,
            'notas' => $this->notas,
            'condiciones' => $this->condiciones,
            'venta_id' => $this->venta_id,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'user' => new UserResource($this->whenLoaded('user')),
            'items' => CotizacionItemResource::collection($this->whenLoaded('items')),
            'venta' => new VentaResource($this->whenLoaded('venta')),
            'estado_label' => $this->estado_label ?? null,
            'estado_color' => $this->estado_color ?? null,
            'estado_icon' => $this->estado_icon ?? null,
            'dias_validez' => $this->dias_validez ?? null,
            'esta_vencida' => $this->esta_vencida ?? null,
            'cantidad_items' => $this->cantidad_items ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
