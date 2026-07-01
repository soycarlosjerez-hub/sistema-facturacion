<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EcfDocumentoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'numero' => $this->numero,
            'tipo_documento' => $this->tipo_documento,
            'estado' => $this->estado,
            'total' => $this->total,
            'fecha' => $this->fecha,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
