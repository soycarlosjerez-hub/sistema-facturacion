<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VentaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ncf' => $this->ncf,
            'ncf_tipo' => $this->ncf_tipo,
            'ncf_vencimiento' => $this->ncf_vencimiento?->toISOString(),
            'tipo_comprobante' => $this->tipo_comprobante,
            'encf' => $this->encf,
            'user_id' => $this->user_id,
            'caja_id' => $this->caja_id,
            'sesion_caja_id' => $this->sesion_caja_id,
            'cliente_id' => $this->cliente_id,
            'tipo_venta_id' => $this->tipo_venta_id,
            'sucursal_id' => $this->sucursal_id,
            'mesa_id' => $this->mesa_id,
            'fecha' => $this->fecha?->toISOString(),
            'subtotal' => (float) $this->subtotal,
            'impuestos' => (float) $this->impuestos,
            'descuento' => (float) $this->descuento,
            'propina' => (float) $this->propina,
            'cargo_servicio' => (float) $this->cargo_servicio,
            'delivery_fee' => (float) $this->delivery_fee,
            'total' => (float) $this->total,
            'estado' => $this->estado,
            'descuento_tipo' => $this->descuento_tipo,
            'descuento_motivo' => $this->descuento_motivo,
            'notas' => $this->notas,
            'tipo_orden' => $this->tipo_orden,
            'delivery_company_id' => $this->delivery_company_id,
            'vehiculo_id' => $this->vehiculo_id,
            'monto_pagado' => $this->montoPagado(),
            'esta_pagada' => $this->estaPagada(),
            'usa_ecf' => $this->usaEcf(),
            'usuario' => new UserResource($this->whenLoaded('usuario')),
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'caja' => new CajaResource($this->whenLoaded('caja')),
            'detalles' => VentaDetalleResource::collection($this->whenLoaded('detalles')),
            'pagos' => PagoResource::collection($this->whenLoaded('pagos')),
            'tipo_venta' => new TipoVentaResource($this->whenLoaded('tipoVenta')),
            'mesa' => new MesaResource($this->whenLoaded('mesa')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
