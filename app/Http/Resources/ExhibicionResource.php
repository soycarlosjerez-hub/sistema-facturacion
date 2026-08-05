<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExhibicionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'lugar' => $this->lugar,
            'fecha_inicio' => $this->fecha_inicio?->toDateString(),
            'fecha_fin' => $this->fecha_fin?->toDateString(),
            'descripcion' => $this->descripcion,
            'tipo' => $this->tipo,
            'tipo_label' => $this->tipo_label,
            'activo' => (bool) $this->activo,
            'esta_activa' => $this->esta_activa,
            'featured_image' => $this->featured_image ? asset('storage/' . $this->featured_image) : null,
            'cantidad_obras' => $this->obras->count(),
            'obras' => ObraPublicResource::collection($this->whenLoaded('obras')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
