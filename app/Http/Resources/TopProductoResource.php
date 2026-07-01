<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'producto_id' => $this['producto_id'],
            'nombre' => $this['nombre'],
            'cantidad_vendida' => $this['cantidad_vendida'],
            'ingresos' => $this['ingresos'],
        ];
    }
}
