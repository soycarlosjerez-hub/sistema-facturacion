<?php

namespace App\Services\Ai\Tools;

use App\Models\Producto;
use Illuminate\Contracts\Auth\Authenticatable;

class GetProductsTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_products';
    }

    public function getDescription(): string
    {
        return 'Obtiene lista de productos del sistema. Accepta filtros opcionales: buscar (nombre, codigo de barras), activo (true/false), stock_bajo (true/false), categoria (ID o nombre). Usa esta herramienta cuando el usuario pregunte por productos, inventario, listado de productos.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'buscar' => [
                    'type' => 'string',
                    'description' => 'Buscar por nombre o codigo de barras. Opcional.',
                ],
                'activo' => [
                    'type' => 'boolean',
                    'description' => 'Filtrar por activo/inactivo. Opcional.',
                ],
                'stock_bajo' => [
                    'type' => 'boolean',
                    'description' => 'Producto con stock entre 1 y stock_minimo. Opcional.',
                ],
                'sin_stock' => [
                    'type' => 'boolean',
                    'description' => 'Producto sin stock (igual o menor a 0). Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Producto::query()->select('id', 'nombre', 'codigo_barras', 'descripcion', 'precio', 'precio_compra', 'stock', 'stock_minimo', 'categoria_id', 'activo')
            ->where('tenant_id', $user->business_instance_id);

        if (!empty($input['buscar'])) {
            $query->where(function ($q) use ($input) {
                $q->where('nombre', 'like', '%'.$input['buscar'].'%')
                  ->orWhere('codigo_barras', 'like', '%'.$input['buscar'].'%');
            });
        }

        if (isset($input['activo'])) {
            $query->where('activo', (bool) $input['activo']);
        }

        if ($input['stock_bajo'] ?? false) {
            $query->whereColumn('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo');
        }

        if ($input['sin_stock'] ?? false) {
            $query->where('stock', '<=', 0);
        }

        $productos = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $productos->count(),
            'productos' => $productos->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'codigo_barras' => $p->codigo_barras,
                    'precio' => number_format($p->precio, 2, '.', ','),
                    'precio_compra' => number_format($p->precio_compra, 2, '.', ','),
                    'stock' => $p->stock,
                    'stock_minimo' => $p->stock_minimo,
                    'activo' => $p->activo,
                ];
            })->toArray(),
        ];
    }
}
