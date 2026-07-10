<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TerminalResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'codigo'    => $this->codigo,
            'ubicacion' => $this->ubicacion,
            'activo'    => $this->activo,
            'caja'      => $this->whenLoaded('caja', fn() => ['id' => $this->caja->id, 'nombre' => $this->caja->nombre]),
        ];
    }
}
