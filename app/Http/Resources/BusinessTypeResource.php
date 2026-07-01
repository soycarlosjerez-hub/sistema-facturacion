<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'color' => $this->color,
            'color_default' => $this->color_default,
            'icon' => $this->icon,
            'icono_default' => $this->icono_default,
            'activo' => $this->activo,
            'orden' => $this->orden,
            'campos_extra' => $this->campos_extra,
            'soft_delete_default' => $this->soft_delete_default,
            'modules' => BusinessTypeResource::formatModules($this->whenLoaded('modules')),
            'visible_modules' => $this->when(! $request->routeIs('business-types.show'), fn () => static::getVisibleModulesForType($this)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    protected static function formatModules($modules)
    {
        if (!$modules) {
            return [];
        }
        return $modules->map(fn ($m) => [
            'id' => $m->id,
            'modulo_key' => $m->modulo_key,
            'nombre' => $m->nombre,
            'icono' => $m->icono,
            'visible' => $m->visible,
            'orden' => $m->orden,
            'ruta' => $m->ruta,
        ])->values()->toArray();
    }

    protected static function getVisibleModulesForType($businessType)
    {
        return \App\Models\BusinessType::getModulosVisibles($businessType->slug);
    }
}
