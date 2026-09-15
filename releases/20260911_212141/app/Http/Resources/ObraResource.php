<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria_id' => $this->categoria_id,
            'titulo' => $this->titulo,
            'codigo_unico' => $this->codigo_unico,
            'slug' => $this->slug,
            'dimensiones' => $this->dimensiones,
            'peso_kg' => (float) $this->peso_kg,
            'medium' => $this->medium,
            'medium_label' => $this->medium_label,
            'technique' => $this->technique,
            'year_created' => $this->year_created,
            'edition_number' => $this->edition_number,
            'edition_total' => $this->edition_total,
            'certificate_number' => $this->certificate_number,
            'photos' => $this->getAllPhotos(),
            'condition_status' => $this->condition_status,
            'condition_status_label' => $this->condition_status_label,
            'creation_date' => $this->creation_date?->toDateString(),
            'exhibition_history' => $this->exhibition_history ?? [],
            'is_original' => (bool) $this->is_original,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'cost_materials' => (float) $this->cost_materials,
            'has_certificate' => $this->getHasCertificateAttribute(),
            'certificate' => new CertificadoAutenticidadResource($this->whenLoaded('certificate')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
