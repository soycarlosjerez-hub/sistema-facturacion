<?php

namespace App\Services\Ai\Tools;

use App\Models\Compra;
use Illuminate\Contracts\Auth\Authenticatable;

class GetPurchasesTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_purchases';
    }

    public function getDescription(): string
    {
        return 'Obtiene compras del sistema. Accepta filtros opcionales: desde (fecha inicio), hasta (fecha fin), proveedor (nombre o ID). Usa esta herramienta cuando el usuario pregunte por compras, historial de compras, proveedores.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'desde' => [
                    'type' => 'string',
                    'description' => 'Fecha inicio (YYYY-MM-DD). Opcional.',
                ],
                'hasta' => [
                    'type' => 'string',
                    'description' => 'Fecha fin (YYYY-MM-DD). Opcional.',
                ],
                'proveedor' => [
                    'type' => 'string',
                    'description' => 'ID o nombre del proveedor. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Compra::query()->where('tenant_id', $user->business_instance_id)->with('proveedor:id,nombre,rnc', 'user:id,name');

        if (!empty($input['desde'])) {
            $query->whereDate('fecha', '>=', $input['desde']);
        }

        if (!empty($input['hasta'])) {
            $query->whereDate('fecha', '<=', $input['hasta']);
        }

        if (!empty($input['proveedor'])) {
            $query->where(function ($q) use ($input) {
                $q->whereHas('proveedor', function ($q2) use ($input) {
                    $q2->where('nombre', 'like', '%'.$input['proveedor'].'%');
                })->orWhereHas('proveedor', function ($q2) use ($input) {
                    $q2->where('id', $input['proveedor']);
                });
            });
        }

        $compras = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $compras->count(),
            'total' => round($compras->sum('total'), 2),
            'compras' => $compras->map(function ($c) {
                return [
                    'id' => $c->id,
                    'proveedor' => $c->proveedor?->nombre,
                    'fecha' => $c->fecha->format('Y-m-d'),
                    'total' => number_format($c->total, 2, '.', ','),
                    'subtotal' => number_format($c->subtotal ?? 0, 2, '.', ','),
                    'itbis_total' => number_format($c->itbis_total ?? 0, 2, '.', ','),
                    'estado' => $c->estado ?? 'n/a',
                ];
            })->toArray(),
        ];
    }
}
