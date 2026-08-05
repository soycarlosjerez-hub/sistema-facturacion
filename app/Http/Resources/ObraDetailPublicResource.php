<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObraDetailPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'codigo_unico' => $this->codigo_unico,
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
            'has_certificate' => $this->getHasCertificateAttribute(),
            'related_works' => ObraPublicResource::collection(
                $this->whenLoaded('relatedWorks') ?? $this->getRelatedWorks()
            ),
        ];
    }

    protected function getRelatedWorks()
    {
        if (!$this->medium) {
            return collect();
        }
        return Obra::where('medium', $this->medium)
            ->where('id', '!=', $this->id)
            ->where('status', 'disponible')
            ->take(4)
            ->get();
    }
}
