<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EncargoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente_id' => $this->cliente_id,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'user_id' => $this->user_id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'boceto_path' => $this->boceto_path ? asset('storage/' . $this->boceto_path) : null,
            'sketch_approved' => (bool) $this->sketch_approved,
            'precio_total' => (float) $this->precio_total,
            'deposito' => (float) $this->deposito,
            'saldo' => (float) $this->saldo,
            'avance_porcentaje' => (int) $this->avance_porcentaje,
            'estimated_completion' => $this->estimated_completion?->toDateString(),
            'actual_completion' => $this->actual_completion?->toDateString(),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'is_overdue' => $this->getIsOverdueAttribute(),
            'notas' => $this->notas,
            'progress_photos' => $this->formatProgressPhotos(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
