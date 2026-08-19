<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use Illuminate\Contracts\Auth\Authenticatable;

class GetInvoicesTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_invoices';
    }

    public function getDescription(): string
    {
        return 'Obtiene facturas (ventas tipo factura) del sistema. Accepta filtros: desde, hasta, tipo_comprobante, estado. Usa esta herramienta cuando el usuario pregunte por facturas.';
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
                    'description' => 'Estado: pagada, pendiente, etc. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Venta::query()
            ->where('tenant_id', $user->business_instance_id)
            ->where('tipo_comprobante', 'factura')
            ->with('cliente:id,nombre');

        if (!empty($input['desde'])) {
            $query->whereDate('created_at', '>=', $input['desde']);
        }

        if (!empty($input['hasta'])) {
            $query->whereDate('created_at', '<=', $input['hasta']);
        }

        if (!empty($input['estado'])) {
            $query->where('estado', $input['estado']);
        }

        $facturas = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $facturas->count(),
            'total' => round($facturas->sum('total'), 2),
            'facturas' => $facturas->map(function ($f) {
                return [
                    'id' => $f->id,
                    'ncf' => $f->ncf,
                    'cliente' => $f->cliente?->nombre,
                    'fecha' => $f->created_at->format('Y-m-d H:i'),
                    'total' => number_format($f->total, 2, '.', ','),
                    'estado' => $f->estado,
                ];
            })->toArray(),
        ];
    }
}
