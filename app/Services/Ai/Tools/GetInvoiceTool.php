<?php

namespace App\Services\Ai\Tools;

use App\Models\Venta;
use Illuminate\Contracts\Auth\Authenticatable;

class GetInvoiceTool implements AiToolInterface
{
    public function getName(): string
    {
        return 'get_invoice';
    }

    public function getDescription(): string
    {
        return 'Obtiene detalle de una factura/venta individual por su ID (venta_id=N) o por su NCF (ncf=C). Usa esta herramienta cuando el usuariomuestre una factura especifica.';
    }

    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'venta_id' => [
                    'type' => 'integer',
                    'description' => 'ID de la venta/factura. Opcional si se envia ncf.',
                ],
                'ncf' => [
                    'type' => 'string',
                    'description' => 'NCF de la factura. Opcional si se envia venta_id.',
                ],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];
    }

    public function execute(array $input, Authenticatable $user): array
    {
        $query = Venta::query()
            ->where('tenant_id', $user->business_instance_id)
            ->where('tipo_comprobante', 'factura')
            ->with([
                'cliente:id,nombre,rnc_cedula',
                'detalles.producto:id,nombre,precio',
            ]);

        if (!empty($input['venta_id'])) {
            $query->where('id', $input['venta_id']);
        } elseif (!empty($input['ncf'])) {
            $query->where('ncf', $input['ncf']);
        } else {
            return ['error' => 'Se requiere venta_id o ncf.'];
        }

        $invoice = $query->first();

        if (!$invoice) {
            return ['error' => 'Factura no encontrada.'];
        }

        $detalles = $invoice->detalles->map(function ($d) {
            return [
                'producto' => $d->producto?->nombre ?? $d->nombre,
                'cantidad' => $d->cantidad,
                'precio_unitario' => number_format($d->precio_unitario ?? $d->precio, 2, '.', ','),
                'subtotal' => number_format($d->subtotal ?? 0, 2, '.', ','),
            ];
        })->toArray();

        return [
            'id' => $invoice->id,
            'ncf' => $invoice->ncf,
            'cliente' => $invoice->cliente?->nombre,
            'fecha' => $invoice->created_at->format('Y-m-d H:i'),
            'estado' => $invoice->estado,
            'subtotal' => number_format($invoice->subtotal ?? 0, 2, '.', ','),
            'impuestos' => number_format($invoice->impuestos ?? 0, 2, '.', ','),
            'descuento' => number_format($invoice->descuento ?? 0, 2, '.', ','),
            'total' => number_format($invoice->total, 2, '.', ','),
            'detalles' => $detalles,
        ];
    }
}
