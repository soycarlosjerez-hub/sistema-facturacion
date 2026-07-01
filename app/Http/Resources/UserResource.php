<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'sucursal_id' => $this->sucursal_id,
            'business_type_id' => $this->business_type_id,
            'business_instance_id' => $this->business_instance_id,
            'instance_role_id' => $this->instance_role_id,
            'is_online' => $this->isOnline(),
            'last_seen_at' => $this->last_seen_at?->toISOString(),
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'business_instance' => new BusinessInstanceResource($this->whenLoaded('businessInstance')),
            'business_type' => new BusinessTypeResource($this->whenLoaded('businessType')),
            'instance_role' => new InstanceRoleResource($this->whenLoaded('instanceRole')),
            'sucursal' => new SucursalResource($this->whenLoaded('sucursal')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
