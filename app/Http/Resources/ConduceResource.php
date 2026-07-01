<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConduceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->folio,
            'cliente_id' => $this->cliente_id,
            'sucursal_id' => $this->sucursal_id,
            'user_id' => $this->user_id,
            'vehiculo_id' => $this->vehiculo_id,
            'fecha_recepcion' => $this->fecha_recepcion,
            'fecha_entrega' => $this->fecha_entrega,
            'estado' => $this->estado,
            'total' => $this->total,
            'kilometraje' => $this->kilometraje,
            'combustible' => $this->combustible,
            'danios' => $this->danios,
            'notas' => $this->notas,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'user' => new UserResource($this->whenLoaded('user')),
            'vehiculo' => new VehiculoResource($this->whenLoaded('vehiculo')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
