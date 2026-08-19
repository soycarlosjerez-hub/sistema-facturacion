<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use Illuminate\Contracts\Auth\Authenticatable;

class GetSaleTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_sale';
    }

    public function getDescription(): string
    {
        return 'Obtiene detalle de una venta individual por su ID o NCF. Usa esta herramienta cuando el usuario pregunte por una venta especifica.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'venta_id' => [
                    'type' => 'integer',
                    'description' => 'ID de la venta. Obligatorio si no se envia ncf.',
                ],
                'ncf' => [
                    'type' => 'string',
                    'description' => 'NCF de la venta. Obligatorio si no se envia venta_id.',
                ],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Venta::with([
            'cliente:id,nombre,rnc_cedula',
            'usuario:id,name',
            'caja:id,nombre',
            'pagos',
            'detalles.producto:id,nombre,precio,precio_compra',
        ])->where('tenant_id', $user->business_instance_id);

        if (!empty($input['venta_id'])) {
            $query->where('id', $input['venta_id']);
        } elseif (!empty($input['ncf'])) {
            $query->where('ncf', $input['ncf']);
        } else {
            return ['error' => 'Se requiere venta_id o ncf.'];
        }

        $venta = $query->first();

        if (!$venta) {
            return ['error' => 'Venta no encontrada.'];
        }

        $detalles = $venta->detalles->map(function ($d) {
            return [
                'producto' => $d->producto?->nombre ?? $d->nombre,
                'cantidad' => $d->cantidad,
                'precio_unitario' => number_format($d->precio_unitario ?? $d->precio, 2, '.', ','),
                'subtotal' => number_format($d->subtotal ?? 0, 2, '.', ','),
            ];
        })->toArray();

        return [
            'id' => $venta->id,
            'ncf' => $venta->ncf,
            'encf' => $venta->encf ?? null,
            'cliente' => $venta->cliente?->nombre,
            'caja' => $venta->caja?->nombre,
            'cajero' => $venta->usuario?->name,
            'fecha' => $venta->created_at->format('Y-m-d H:i'),
            'estado' => $venta->estado,
            'subtotal' => number_format($venta->subtotal ?? 0, 2, '.', ','),
            'impuestos' => number_format($venta->impuestos ?? 0, 2, '.', ','),
            'descuento' => number_format($venta->descuento ?? 0, 2, '.', ','),
            'total' => number_format($venta->total, 2, '.', ','),
            'detalles' => $detalles,
            'pagos' => $venta->pagos->map(fn($p) => [
                'metodo' => $p->metodo_pago,
                'monto' => number_format($p->monto, 2, '.', ','),
            ])->toArray(),
        ];
    }
}
