<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Contracts\Auth\Authenticatable;

class GetSalesTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_sales';
    }

    public function getDescription(): string
    {
        return 'Obtiene ventas del sistema. Accepta filtros opcionales: desde (fecha inicio, formato YYYY-MM-DD), hasta (fecha fin, formato YYYY-MM-DD), estado (pendiente, pagada, etc), cliente (nombre o ID). Usa esta herramienta cuando el usuario pregunte por ventas, historial de ventas, totales, etc.';
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
                'estado' => [
                    'type' => 'string',
                    'description' => 'Filtrar por estado (pendiente, pagada, etc). Opcional.',
                ],
                'cliente' => [
                    'type' => 'string',
                    'description' => 'Nombre o ID del cliente. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Venta::query()
            ->with('cliente:id,nombre,rnc_cedula', 'usuario:id,name,caja:id,nombre', 'pagos');

        if (isset($input['desde'])) {
            $query->whereDate('created_at', '>=', $input['desde']);
        }

        if (isset($input['hasta'])) {
            $query->whereDate('created_at', '<=', $input['hasta']);
        }

        if (isset($input['estado'])) {
            $query->where('estado', $input['estado']);
        }

        if (isset($input['cliente'])) {
            $query->where(function ($q) use ($input) {
                $q->whereHas('cliente', function ($q2) use ($input) {
                    $q2->where('nombre', 'like', '%'.$input['cliente'].'%');
                })->orWhereHas('cliente', function ($q2) use ($input) {
                    $q2->where('id', $input['cliente']);
                });
            });
        }

        $ventas = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $ventas->count(),
            'total' => round($ventas->sum('total'), 2),
            'ventas' => $ventas->map(function ($v) {
                return [
                    'id' => $v->id,
                    'ncf' => $v->ncf,
                    'cliente' => $v->cliente?->nombre,
                    'cajero' => $v->usuario?->name,
                    'fecha' => $v->created_at->format('Y-m-d H:i'),
                    'total' => number_format($v->total, 2, '.', ','),
                    'estado' => $v->estado,
                    'subtotal' => number_format($v->subtotal ?? 0, 2, '.', ','),
                    'impuestos' => number_format($v->impuestos ?? 0, 2, '.', ','),
                ];
            })->toArray(),
        ];
    }
}
