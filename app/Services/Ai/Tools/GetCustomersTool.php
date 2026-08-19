<?php

namespace App\Services\Ai\Tools;

use App\Models\Cliente;
use Illuminate\Contracts\Auth\Authenticatable;

class GetCustomersTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_customers';
    }

    public function getDescription(): string
    {
        return 'Obtiene lista de clientes del sistema. Accepta filtros opcionales: buscar (nombre, RNC o cedula), activo (true/false), con_deuda (true/false). Usa esta herramienta cuando el usuario pregunte por clientes, lista de clientes, clientes con deuda, etc.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'buscar' => [
                    'type' => 'string',
                    'description' => 'Buscar por nombre, RNC o cedula. Opcional.',
                ],
                'activo' => [
                    'type' => 'boolean',
                    'description' => 'Filtrar por activo/inactivo. Opcional.',
                ],
                'con_deuda' => [
                    'type' => 'boolean',
                    'description' => 'Solo clientes con balance pendiente mayor a 0. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Cliente::query()->select('id', 'nombre', 'rnc_cedula', 'rnc', 'email', 'telefono', 'balance_pendiente', 'limite_credito', 'activo')
            ->where('tenant_id', $user->business_instance_id);

        if (!empty($input['buscar'])) {
            $query->where(function ($q) use ($input) {
                $q->where('nombre', 'like', '%'.$input['buscar'].'%')
                  ->orWhere('rnc_cedula', 'like', '%'.$input['buscar'].'%')
                  ->orWhere('rnc', 'like', '%'.$input['buscar'].'%');
            });
        }

        if (isset($input['activo'])) {
            $query->where('activo', (bool) $input['activo']);
        }

        if ($input['con_deuda'] ?? false) {
            $query->where('balance_pendiente', '>', 0);
        }

        $clientes = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $clientes->count(),
            'clientes' => $clientes->map(function ($c) {
                return [
                    'id' => $c->id,
                    'nombre' => $c->nombre,
                    'rnc_cedula' => $c->rnc_cedula,
                    'email' => $c->email,
                    'telefono' => $c->telefono,
                    'balance_pendiente' => number_format($c->balance_pendiente, 2, '.', ','),
                    'limite_credito' => number_format($c->limite_credito, 2, '.', ','),
                    'activo' => $c->activo,
                ];
            })->toArray(),
        ];
    }
}
