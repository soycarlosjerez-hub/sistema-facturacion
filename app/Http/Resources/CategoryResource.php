<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $types = $this->whenLoaded('businessTypes', function () {
            return $this->businessTypes->map(fn ($bt) => [
                'key' => $bt->key,
                'slug' => $bt->slug,
                'nombre' => $bt->nombre,
                'color_default' => $bt->color_default,
                'icono_default' => $bt->icono_default,
                'pivot' => $this->pivotData($bt),
            ]);
        });

        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'activa' => $this->activa,
            'color' => $this->color,
            'icono' => $this->icono,
            'orden' => $this->orden,
            'configuracion' => $this->configuracion,
            'types' => $types,
            'productos_count' => $this->whenLoaded('products', fn () => $this->products->count()),
            'mesas_count' => $this->whenLoaded('tables', fn () => $this->tables->count()),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'deleted_at' => $this->when($this->deleted_at, fn () => $this->deleted_at->toISOString()),
        ];
    }

    protected function pivotData($businessType): array
    {
        $pivot = $this->businessTypes()
            ->where('business_types.key', $businessType->key)
            ->first()?->pivot;

        if (!$pivot) return [];

        return [
            'configuracion' => $pivot->configuracion,
            'soft_delete_enabled' => $pivot->soft_delete_enabled,
            'orden' => $pivot->orden ?? null,
        ];
    }
}