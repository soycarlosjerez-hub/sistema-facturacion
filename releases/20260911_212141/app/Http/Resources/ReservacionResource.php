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
            'cliente_nombre' => $this->cliente_nombre,
            'cliente_telefono' => $this->cliente_telefono,
            'cliente_email' => $this->cliente_email,
            'mesa_id' => $this->mesa_id,
            'personas' => $this->personas,
            'fecha_hora' => $this->fecha_hora?->toISOString(),
            'estado' => $this->estado,
            'notas' => $this->notas,
            'user_id' => $this->user_id,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'mesa' => new MesaResource($this->whenLoaded('mesa')),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
