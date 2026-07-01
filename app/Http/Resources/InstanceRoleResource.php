<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstanceRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'business_instance_id' => $this->business_instance_id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'modules' => InstanceRoleResource::formatModules($this->whenLoaded('modules')),
            'visible_modules' => $this->whenLoaded('modules', fn () => $this->modules->where('is_visible', true)->pluck('modulo_key')->toArray()),
            'users_count' => $this->whenLoaded('users', fn () => $this->users->count()),
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
            'is_visible' => $m->is_visible,
            'orden' => $m->orden,
        ])->values()->toArray();
    }
}
