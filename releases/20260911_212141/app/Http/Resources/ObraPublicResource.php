<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObraPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'medium' => $this->medium,
            'medium_label' => $this->medium_label,
            'technique' => $this->technique,
            'year_created' => $this->year_created,
            'dimensiones' => $this->dimensiones,
            'photos' => $this->getPrimaryPhotos(),
            'is_original' => (bool) $this->is_original,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'condition_status' => $this->condition_status,
            'condition_status_label' => $this->condition_status_label,
            'has_certificate' => $this->getHasCertificateAttribute(),
        ];
    }
}
