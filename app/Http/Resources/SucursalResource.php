<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SucursalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'rnc' => $this->rnc,
            'activa' => $this->activa,
            'es_matriz' => $this->es_matriz,
            'tenant_id' => $this->tenant_id,
            'usuarios_count' => $this->whenLoaded('usuarios', fn () => $this->usuarios->count()),
            'ventas_count' => $this->whenLoaded('ventas', fn () => $this->ventas->count()),
            'compras_count' => $this->whenLoaded('compras', fn () => $this->compras->count()),
            'cajas_count' => $this->whenLoaded('cajas', fn () => $this->cajas->count()),
            'gastos_count' => $this->whenLoaded('gastos', fn () => $this->gastos->count()),
            'mesas_count' => $this->whenLoaded('mesas', fn () => $this->mesas->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->when($this->deleted_at, fn () => $this->deleted_at->toISOString()),
        ];
    }
}
