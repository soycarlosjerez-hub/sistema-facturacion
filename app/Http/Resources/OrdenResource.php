<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrdenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'tipo_orden'       => $this->tipo_orden,
            'estado'           => $this->estado,
            'subtotal'         => $this->subtotal,
            'impuestos'        => $this->impuestos,
            'descuento'        => $this->descuento,
            'total'            => $this->total,
            'propina'          => $this->propina,
            'cargo_servicio'   => $this->cargo_servicio,
            'delivery_fee'     => $this->delivery_fee,
            'direccion_entrega'=> $this->direccion_entrega,
            'telefono_contacto'=> $this->telefono_contacto,
            'hora_retiro'      => $this->hora_retiro instanceof \Carbon\Carbon ? $this->hora_retiro->toIso8601String() : $this->hora_retiro,
            'notas'            => $this->notas,
            'ncf'              => $this->ncf,
            'tipo_comprobante' => $this->tipo_comprobante,
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),

            'terminal'    => $this->whenLoaded('terminal', fn() => $this->terminal ? TerminalResource::make($this->terminal) : null),
            'usuario'     => $this->whenLoaded('usuario', fn() => $this->usuario ? ['id' => $this->usuario->id, 'name' => $this->usuario->name] : null),
            'caja'        => $this->whenLoaded('caja', fn() => $this->caja ? ['id' => $this->caja->id, 'nombre' => $this->caja->nombre] : null),
            'cliente'     => $this->whenLoaded('cliente', fn() => $this->cliente ? ClienteResource::make($this->cliente) : null),
            'detalles'    => OrdenDetalleResource::collection($this->whenLoaded('detalles')),
            'pagos'       => $this->whenLoaded('pagos', fn() => $this->pagos?->toArray()),
            'entrega_empresa' => $this->whenLoaded('entregaEmpresa', fn() => $this->entregaEmpresa ? ['id' => $this->entregaEmpresa->id, 'nombre' => $this->entregaEmpresa->nombre] : null),
        ];
    }
}
