<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventarioBajoStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'producto_id' => $this['producto_id'],
            'nombre' => $this['nombre'],
            'stock_actual' => $this['stock_actual'],
            'stock_minimo' => $this['stock_minimo'],
            'unidad_medida' => $this['unidad_medida'],
        ];
    }
}
