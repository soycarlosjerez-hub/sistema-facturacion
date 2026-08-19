<?php

namespace App\Services\Ai\Tools;

use App\Models\Compra;
use App\Models\Proveedor;
use Illuminate\Contracts\Auth\Authenticatable;

class GetAccountsPayableTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_accounts_payable';
    }

    public function getDescription(): string
    {
        return 'Obtiene cuentas por pagar: proveedores a los que se debe por compras pendientes de pago. Usa esta herramienta cuando el usuario pregunte por cuentas por pagar, deudas con proveedores.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'buscar' => [
                    'type' => 'string',
                    'description' => 'Buscar por nombre del proveedor. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Proveedor::query()
            ->select('id', 'nombre', 'rnc', 'activo')
            ->withCount(['compras as total_compras' => function ($q) {
                // Todas las compras de este proveedor
            }]);

        if (!empty($input['buscar'])) {
            $query->where('nombre', 'like', '%'.$input['buscar'].'%');
        }

        $proveedores = $query->take(config('ai.max_tool_results', 50))->get();

        $cuentas = [];
        $totalDeuda = 0;

        foreach ($proveedores as $proveedor) {
            $totalCompras = Compra::where('proveedor_id', $proveedor->id)->sum('total');
            if ($totalCompras > 0) {
                $cuentas[] = [
                    'proveedor_id' => $proveedor->id,
                    'nombre' => $proveedor->nombre,
                    'rnc' => $proveedor->rnc,
                    'total_compras' => number_format($totalCompras, 2, '.', ','),
                ];
                $totalDeuda += $totalCompras;
            }
        }

        return [
            'cantidad_proveedores' => count($cuentas),
            'total_deuda' => round($totalDeuda, 2),
            'cuentas' => $cuentas,
        ];
    }
}
