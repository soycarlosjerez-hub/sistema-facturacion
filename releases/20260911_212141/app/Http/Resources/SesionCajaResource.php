<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SesionCajaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'caja_id' => $this->caja_id,
            'user_id' => $this->user_id,
            'estado' => $this->estado,
            'fecha_apertura' => $this->fecha_apertura?->toISOString(),
            'fecha_cierre' => $this->fecha_cierre?->toISOString(),
            'monto_inicial' => (float) $this->monto_inicial,
            'monto_final' => (float) $this->monto_final,
            'ventas_count' => $this->whenLoaded('ventas', fn () => $this->ventas->count()),
            'egresos_total' => (float) $this->egresos_total,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
