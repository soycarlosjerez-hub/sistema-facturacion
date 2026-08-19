<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Contracts\Auth\Authenticatable;

class GetAccountsReceivableTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_accounts_receivable';
    }

    public function getDescription(): string
    {
        return 'Obtiene cuentas por cobrar: clientes con deuda pendiente (ventas pendientes o en cuenta abierta). Usa esta herramienta cuando el usuario pregunte por cuentas por cobrar, deudas de clientes, clientes con saldo pendiente.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'buscar' => [
                    'type' => 'string',
                    'description' => 'Buscar por nombre de cliente. Opcional.',
                ],
                'mayor_a' => [
                    'type' => 'number',
                    'description' => 'Solo deudas mayores a este monto. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Cliente::query()
            ->select('id', 'nombre', 'rnc_cedula', 'balance_pendiente')
            ->where('tenant_id', $user->business_instance_id)
            ->where('balance_pendiente', '>', 0);

        if (!empty($input['buscar'])) {
            $query->where('nombre', 'like', '%'.$input['buscar'].'%');
        }

        if (isset($input['mayor_a'])) {
            $query->where('balance_pendiente', '>', (float) $input['mayor_a']);
        }

        $clientes = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad_clientes' => $clientes->count(),
            'total_cobrar' => round($clientes->sum('balance_pendiente'), 2),
            'cuentas' => $clientes->map(function ($c) {
                return [
                    'cliente_id' => $c->id,
                    'nombre' => $c->nombre,
                    'rnc_cedula' => $c->rnc_cedula,
                    'saldo_pendiente' => number_format($c->balance_pendiente, 2, '.', ','),
                ];
            })->toArray(),
        ];
    }
}
