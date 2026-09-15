<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevolucionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'venta_id' => $this->venta_id,
            'cliente_id' => $this->cliente_id,
            'sucursal_id' => $this->sucursal_id,
            'user_id' => $this->user_id,
            'fecha' => $this->fecha,
            'motivo' => $this->motivo,
            'tipo' => $this->tipo,
            'subtotal' => $this->subtotal,
            'itbis' => $this->itbis,
            'total' => $this->total,
            'estado' => $this->estado,
            'nota_credito_id' => $this->nota_credito_id,
            'venta' => new VentaResource($this->whenLoaded('venta')),
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'user' => new UserResource($this->whenLoaded('user')),
            'detalles' => DetalleDevolucionResource::collection($this->whenLoaded('detalles')),
            'notaCredito' => new EcfDocumentoResource($this->whenLoaded('notaCredito')),
            'tiene_ecf' => $this->tiene_ecf ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
