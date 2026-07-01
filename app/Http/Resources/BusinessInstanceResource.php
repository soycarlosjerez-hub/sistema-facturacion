<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessInstanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'slug' => $this->slug,
            'rnc' => $this->rnc,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'business_type_id' => $this->business_type_id,
            'owner_user_id' => $this->owner_user_id,
            'configuracion' => $this->configuracion,
            'activo' => $this->activo,
            'fecha_vencimiento' => $this->fecha_vencimiento?->toISOString(),
            'costo_mensual' => (float) $this->costo_mensual,
            'bloqueado' => $this->bloqueado,
            'motivo_bloqueo' => $this->motivo_bloqueo,
            'bloqueado_en' => $this->bloqueado_en?->toISOString(),
            'setup_completed' => $this->setup_completed,
            'esta_al_dia' => $this->estaAlDia(),
            'meses_atrasados' => $this->mesesAtrasados(),
            'deuda_estimada' => $this->deudaEstimada(),
            'proximo_pago_esperado' => $this->proximoPagoEsperado()?->toISOString(),
            'business_type' => new BusinessTypeResource($this->whenLoaded('businessType')),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'users_count' => $this->whenLoaded('users', fn () => $this->users->count()),
            'sucursales_count' => $this->whenLoaded('sucursales', fn () => $this->sucursales->count()),
            'modules' => BusinessInstanceResource::formatModules($this->whenLoaded('modules')),
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
            'modulo_key' => $m->modulo_key,
            'visible' => $m->visible,
            'orden' => $m->orden,
        ])->values()->toArray();
    }
}
