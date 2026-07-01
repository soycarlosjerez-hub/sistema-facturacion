<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'categoria_id' => $this->categoria_id,
            'nombre' => $this->nombre,
            'codigo_barras' => $this->codigo_barras,
            'descripcion' => $this->descripcion,
            'precio' => (float) $this->precio,
            'precio_compra' => (float) $this->precio_compra,
            'unidad_medida' => $this->unidad_medida,
            'itbis_porcentaje' => (float) $this->itbis_porcentaje,
            'stock' => $this->stock,
            'stock_minimo' => $this->stock_minimo,
            'imagen' => $this->imagen,
            'tenant_id' => $this->tenant_id,
            'ganancia' => $this->ganancia,
            'margen_porcentaje' => $this->margen_porcentaje,
            'estado_stock' => $this->estado_stock,
            'imagen_url' => $this->imagen_url,
            'tiene_imagen' => $this->tiene_imagen,
            'categoria' => new CategoriaResource($this->whenLoaded('categoria')),
            'ingredientes' => ProductoResource::formatIngredientes($this->whenLoaded('ingredientes')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    protected static function formatIngredientes($ingredientes)
    {
        if (!$ingredientes) {
            return [];
        }
        return $ingredientes->map(fn ($i) => [
            'id' => $i->id,
            'nombre' => $i->nombre,
            'pivot_cantidad' => $i->pivot->cantidad ?? null,
        ])->values()->toArray();
    }
}
