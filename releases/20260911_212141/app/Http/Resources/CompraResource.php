<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'proveedor_id' => $this->proveedor_id,
            'sucursal_id' => $this->sucursal_id,
            'almacen_id' => $this->almacen_id,
            'tipo_compra_id' => $this->tipo_compra_id,
            'user_id' => $this->user_id,
            'total' => (float) $this->total,
            'subtotal' => (float) $this->subtotal,
            'itbis_total' => (float) $this->itbis_total,
            'fecha' => $this->fecha?->toISOString(),
            'observaciones' => $this->observaciones,
            'aplica_retencion_isr' => $this->aplica_retencion_isr,
            'aplica_retencion_itbis' => $this->aplica_retencion_itbis,
            'retencion_isr' => (float) $this->retencion_isr,
            'retencion_itbis' => (float) $this->retencion_itbis,
            'total_neto' => (float) $this->total_neto,
            'total_retenciones' => $this->total_retenciones,
            'total_pagar' => $this->total_pagar,
            'folio' => $this->folio,
            'puede_generar_ecf' => $this->puedeGenerarEcf,
            'proveedor' => new ProveedorResource($this->whenLoaded('proveedor')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'almacen' => new AlmacenResource($this->whenLoaded('almacen')),
            'user' => new UserResource($this->whenLoaded('user')),
            'tipo_compra' => new TipoCompraResource($this->whenLoaded('tipoCompra')),
            'detalles' => DetalleCompraResource::collection($this->whenLoaded('detalles')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
