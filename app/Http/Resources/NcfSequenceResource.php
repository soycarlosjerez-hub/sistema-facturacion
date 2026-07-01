<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NcfSequenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sucursal_id' => $this->sucursal_id,
            'tipo_ncf' => $this->tipo_ncf,
            'inicio' => $this->inicio,
            'fin' => $this->fin,
            'activa' => $this->activa,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
