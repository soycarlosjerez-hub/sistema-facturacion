<?php

namespace App\Services\Ai\Tools;

use App\Models\Proveedor;
use Illuminate\Contracts\Auth\Authenticatable;

class GetSuppliersTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_suppliers';
    }

    public function getDescription(): string
    {
        return 'Obtiene lista de proveedores del sistema. Accepta filtros opcionales: buscar (nombre, RNC), activo (true/false). Usa esta herramienta cuando el usuario pregunte por proveedores.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'buscar' => [
                    'type' => 'string',
                    'description' => 'Buscar por nombre o RNC. Opcional.',
                ],
                'activo' => [
                    'type' => 'boolean',
                    'description' => 'Filtrar por activo/inactivo. Opcional.',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Proveedor::query()->select('id', 'nombre', 'email', 'telefono', 'rnc', 'activo')
            ->where('tenant_id', $user->business_instance_id);

        if (!empty($input['buscar'])) {
            $query->where(function ($q) use ($input) {
                $q->where('nombre', 'like', '%'.$input['buscar'].'%')
                  ->orWhere('rnc', 'like', '%'.$input['buscar'].'%');
            });
        }

        if (isset($input['activo'])) {
            $query->where('activo', (bool) $input['activo']);
        }

        $proveedores = $query->take(config('ai.max_tool_results', 50))->get();

        return [
            'cantidad' => $proveedores->count(),
            'proveedores' => $proveedores->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'rnc' => $p->rnc,
                    'email' => $p->email,
                    'telefono' => $p->telefono,
                    'activo' => $p->activo,
                ];
            })->toArray(),
        ];
    }
}
