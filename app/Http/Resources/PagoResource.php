<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PagoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'venta_id' => $this->venta_id,
            'caja_id' => $this->caja_id,
            'sesion_caja_id' => $this->sesion_caja_id,
            'monto' => (float) $this->monto,
            'metodo_pago' => $this->metodo_pago,
            'payment_processor_id' => $this->payment_processor_id,
            'nota' => $this->nota,
            'fecha_pago' => $this->fecha_pago?->toISOString(),
            'venta' => new VentaResource($this->whenLoaded('venta')),
            'caja' => new CajaResource($this->whenLoaded('caja')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
