<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificadoAutenticidadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'obra_id' => $this->obra_id,
            'obra' => new ObraPublicResource($this->whenLoaded('obra')),
            'numero_certificado' => $this->numero_certificado,
            'qr_code' => $this->qr_code_url,
            'firmado_en' => $this->firmado_en?->toDateString(),
            'expirado' => (bool) $this->expirado,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
