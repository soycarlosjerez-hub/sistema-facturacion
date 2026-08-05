<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsignacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'galeria_nombre' => $this->galeria_nombre,
            'obra_id' => $this->obra_id,
            'obra' => new ObraPublicResource($this->whenLoaded('obra')),
            'fecha_inicio' => $this->fecha_inicio?->toDateString(),
            'fecha_fin' => $this->fecha_fin?->toDateString(),
            'comision_percentage' => (float) $this->comision_percentage,
            'estado' => $this->estado,
            'estado_label' => $this->estado_label,
            'estado_color' => $this->estado_color,
            'fecha_venta' => $this->fecha_venta?->toDateString(),
            'precio_venta' => $this->precio_venta ? (float) $this->precio_venta : null,
            'comision_monto' => $this->comision_monto ? (float) $this->comision_monto : null,
            'pago_recibido' => (bool) $this->pago_recibido,
            'pago_fecha' => $this->pago_fecha?->toDateString(),
            'is_expired' => $this->getIsExpiredAttribute(),
            'notas' => $this->notas,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
