<?php

namespace App\Services\Ai\Tools;

use App\Models\Producto;
use Illuminate\Contracts\Auth\Authenticatable;

class GetProductTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_product';
    }

    public function getDescription(): string
    {
        return 'Obtiene detalle de un producto individual por ID o codigo de barras. Usa esta herramienta cuando el usuario pregunte por informacion de un producto especifico.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'producto_id' => [
                    'type' => 'integer',
                    'description' => 'ID del producto. Opcional.',
                ],
                'codigo_barras' => [
                    'type' => 'string',
                    'description' => 'Codigo de barras del producto. Opcional.',
                ],
                'nombre' => [
                    'type' => 'string',
                    'description' => 'Nombre exacto del producto. Opcional.',
                ],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Producto::query()->select('id', 'nombre', 'codigo_barras', 'descripcion', 'marca', 'modelo', 'precio', 'precio_compra', 'stock', 'stock_minimo', 'unidad_medida', 'itbis_porcentaje', 'activo')
            ->where('tenant_id', $user->business_instance_id);

        if (!empty($input['producto_id'])) {
            $query->where('id', $input['producto_id']);
        } elseif (!empty($input['codigo_barras'])) {
            $query->where('codigo_barras', $input['codigo_barras']);
        } elseif (!empty($input['nombre'])) {
            $query->where('nombre', $input['nombre']);
        }

        $producto = $query->first();

        if (!$producto) {
            return ['error' => 'Producto no encontrado.'];
        }

        $ganancia = round(($producto->precio - ($producto->precio_compra ?? 0)), 2);

        return [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'codigo_barras' => $producto->codigo_barras,
            'descripcion' => $producto->descripcion,
            'marca' => $producto->marca,
            'modelo' => $producto->modelo,
            'precio' => number_format($producto->precio, 2, '.', ','),
            'precio_compra' => number_format($producto->precio_compra, 2, '.', ','),
            'ganancia' => number_format($ganancia, 2, '.', ','),
            'stock' => $producto->stock,
            'stock_minimo' => $producto->stock_minimo,
            'unidad_medida' => $producto->unidad_medida,
            'itbis_porcentaje' => $producto->itbis_porcentaje,
            'activo' => $producto->activo,
        ];
    }
}
