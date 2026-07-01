<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'mesa_id' => $this->mesa_id,
            'estado' => $this->estado,
            'fecha_hora' => $this->fecha_hora?->toISOString(),
            'personas' => $this->personas,
            'notas' => $this->notas,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'mesa' => new MesaResource($this->whenLoaded('mesa')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
