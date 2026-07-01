<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImpresoraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'tipo' => $this->tipo,
            'sucursal_id' => $this->sucursal_id,
            'puerto' => $this->puerto,
            'ip' => $this->ip,
            'activa' => $this->activa,
            'configuracion' => $this->configuracion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
