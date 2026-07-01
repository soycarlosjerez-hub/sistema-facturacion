<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstanceRoleModuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'instance_role_id' => $this->instance_role_id,
            'modulo_key' => $this->modulo_key,
            'is_visible' => $this->is_visible,
            'orden' => $this->orden,
            'instance_role' => new InstanceRoleResource($this->whenLoaded('instanceRole')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
