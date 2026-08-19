<?php

namespace App\Services\Ai\Tools;

use App\Models\Gasto;
use Illuminate\Contracts\Auth\Authenticatable;

class GetExpensesTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_expenses';
    }

    public function getDescription(): string
    {
        return 'Obtiene gastos registrados. Accepta filtros opcionales: desde, hasta, categoria (servicios, suministros, mantenimiento, salarios, impuestos, transporte, publicidad, alimentacion, otros). Usa esta herramienta cuando el usuario pregunte por gastos.';
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
                'categoria' => [
                    'type' => 'string',
                    'description' => 'Filtrar por categoria de gasto. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Gasto::query()->where('tenant_id', $user->business_instance_id)->with('user:id,name');

        if (!empty($input['desde'])) {
            $query->whereDate('fecha_gasto', '>=', $input['desde']);
        }

        if (!empty($input['hasta'])) {
            $query->whereDate('fecha_gasto', '<=', $input['hasta']);
        }

        if (!empty($input['categoria'])) {
            $query->where('categoria', $input['categoria']);
        }

        $gastos = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $gastos->count(),
            'total' => round($gastos->sum('monto'), 2),
            'gastos' => $gastos->map(function ($g) {
                return [
                    'id' => $g->id,
                    'descripcion' => $g->descripcion,
                    'categoria' => $g->categoria,
                    'monto' => number_format($g->monto, 2, '.', ','),
                    'fecha' => $g->fecha_gasto->format('Y-m-d'),
                    'cajero' => $g->user?->name,
                ];
            })->toArray(),
        ];
    }
}
