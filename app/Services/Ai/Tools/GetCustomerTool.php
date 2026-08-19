<?php

namespace App\Services\Ai\Tools;

use App\Models\Cliente;
use Illuminate\Contracts\Auth\Authenticatable;

class GetCustomerTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_customer';
    }

    public function getDescription(): string
    {
        return 'Obtiene detalle de un cliente individual por ID, nombre, RNC o cedula. Usa esta herramienta cuando el usuario pregunte por informacion de un cliente especifico.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'cliente_id' => [
                    'type' => 'integer',
                    'description' => 'ID del cliente. Opcional.',
                ],
                'nombre' => [
                    'type' => 'string',
                    'description' => 'Nombre exacto del cliente. Opcional.',
                ],
                'rnc' => [
                    'type' => 'string',
                    'description' => 'RNC o cedula del cliente. Opcional.',
                ],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Cliente::query()->select('id', 'nombre', 'rnc_cedula', 'rnc', 'email', 'telefono', 'direccion', 'balance_pendiente', 'limite_credito', 'activo', 'tipo_cliente', 'moneda', 'tipo_documento')
            ->where('tenant_id', $user->business_instance_id);

        if (!empty($input['cliente_id'])) {
            $query->where('id', $input['cliente_id']);
        } elseif (!empty($input['nombre'])) {
            $query->where('nombre', $input['nombre']);
        } elseif (!empty($input['rnc'])) {
            $query->where(function ($q) use ($input) {
                $q->where('rnc_cedula', $input['rnc'])
                  ->orWhere('rnc', $input['rnc']);
            });
        }

        $cliente = $query->first();

        if (!$cliente) {
            return ['error' => 'Cliente no encontrado.'];
        }

        $ventasCount =  \App\Models\Venta::where('tenant_id', $user->business_instance_id)->where('cliente_id', $cliente->id)->count();
        $totalVentas =  \App\Models\Venta::where('tenant_id', $user->business_instance_id)->where('cliente_id', $cliente->id)->sum('total');
        $pendiente = (float) $cliente->balance_pendiente;

        return [
            'id' => $cliente->id,
            'nombre' => $cliente->nombre,
            'rnc_cedula' => $cliente->rnc_cedula,
            'rnc' => $cliente->rnc,
            'email' => $cliente->email,
            'telefono' => $cliente->telefono,
            'direccion' => $cliente->direccion,
            'tipo_cliente' => $cliente->tipo_cliente,
            'activo' => $cliente->activo,
            'balance_pendiente' => number_format($pendiente, 2, '.', ','),
            'limite_credito' => number_format($cliente->limite_credito, 2, '.', ','),
            'cantidad_ventas' => $ventasCount,
            'total_compras' => number_format($totalVentas, 2, '.', ','),
        ];
    }
}
