<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cliente_id' => $this['cliente_id'],
            'nombre' => $this['nombre'],
            'compras_total' => $this['compras_total'],
            'monto_gastado' => $this['monto_gastado'],
        ];
    }
}
