<?php

namespace App\Services\Ai\Tools;

use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Contracts\Auth\Authenticatable;

class GetInventoryTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_inventory';
    }

    public function getDescription(): string
    {
        return 'Obtiene resumen del inventario/stock del sistema: total de productos, productos con bajo stock, productos sin stock, valor total del inventario. Usa esta herramienta cuando el usuario pregunte por inventario, stock, existencias.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'filtro' => [
                    'type' => 'string',
                    'description' => 'Filtro: "todos", "bajo_stock", "sin_stock". Default: "todos". Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $filtros = $input['filtro'] ?? 'todos';
        $query = Producto::query()->where('tenant_id', $user->business_instance_id);

        if ($filtros === 'bajo_stock') {
            $query->where('stock', '>', 0)->whereColumn('stock', '<=', 'stock_minimo');
        } elseif ($filtros === 'sin_stock') {
            $query->where('stock', '<=', 0);
        }

        $productos = $query->take(config('ai.max_tool_results', 50))->get();

        $totalProductos = $productos->count();
        $totalValorInventario = $productos->sum(fn($p) => $p->stock * ($p->precio_compra ?? 0));
        $bajoStock = $productos->where(function ($p) { return $p->stock > 0 && $p->stock <= ($p->stock_minimo ?? 0); })->count();
        $sinStock = $productos->where('stock', '<=', 0)->count();
        $conStock = $productos->where('stock', '>', 0)->count();

        return [
            'total_productos' => $totalProductos,
            'con_stock' => $conStock,
            'bajo_stock' => $bajoStock,
            'sin_stock' => $sinStock,
            'valor_total_inventario' => number_format($totalValorInventario, 2, '.', ','),
            'moneda' => 'RD$',
            'filtro' => $filtros,
        ];
    }
}
