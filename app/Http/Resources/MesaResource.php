<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MesaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sucursal_id' => $this->sucursal_id,
            'numero' => $this->numero,
            'nombre' => $this->nombre,
            'capacidad' => $this->capacidad,
            'ubicacion_id' => $this->ubicacion_id,
            'estado' => $this->estado,
            'activa' => $this->activa,
            'categoria_id' => $this->categoria_id,
            'pos_x' => $this->pos_x,
            'pos_y' => $this->pos_y,
            'tenant_id' => $this->tenant_id,
            'categoria' => new MesaCategoriaResource($this->whenLoaded('categoria')),
            'ubicacion' => new MesaUbicacionResource($this->whenLoaded('ubicacion')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'orden_activa' => new VentaResource($this->whenLoaded('ordenActiva')),
            'reservacion' => new ReservacionResource($this->whenLoaded('reservacion')),
            'ventas_count' => $this->whenLoaded('ventas', fn () => $this->ventas->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
