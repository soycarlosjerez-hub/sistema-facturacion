<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Support\RetencionCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function createPurchase(array $data, array $products): Compra
    {
        $products = $this->filterEmptyRows($products);
        $totals = $this->calculateTotals($products);

        $newProducts = [];
        $updatedProducts = [];

        return DB::transaction(function () use ($data, $products, $totals, &$newProducts, &$updatedProducts) {
            $compra = Compra::create([
                'proveedor_id'    => $data['proveedor_id'],
                'sucursal_id'     => session('sucursal_id'),
                'almacen_id'      => $data['almacen_id'] ?? null,
                'tipo_compra_id'  => $data['tipo_compra_id'],
                'user_id'         => Auth::id(),
                'total'           => $totals['total'],
                'itbis_total'     => $totals['itbis_total'],
                'subtotal'        => $totals['subtotal'],
                'fecha'           => $data['fecha'] ?? now(),
                'observaciones'   => $data['observaciones'] ?? null,
            ]);

            foreach ($products as $item) {
                $producto = $this->resolveOrCreateProduct($item, $newProducts, $updatedProducts);

                $detalle = DetalleCompra::create([
                    'compra_id'         => $compra->id,
                    'producto_id'       => $producto->id,
                    'cantidad'          => $item['cantidad'],
                    'precio_unitario'   => $item['precio'],
                    'itbis_porcentaje'  => $item['itbis_porcentaje'] ?? 18,
                    'subtotal'          => $this->computeDetailSubtotal($item),
                ]);

                $this->createInventoryMovement($compra, $detalle, $producto, $item['cantidad']);
            }

            $this->applyRetentions($compra, $data, $totals);

            $compra->setRelation('newProducts', $newProducts);
            $compra->setRelation('updatedProducts', $updatedProducts);

            return $compra;
        });
    }

    public function updatePurchase(Compra $compra, array $data, array $products): Compra
    {
        $products = $this->filterEmptyRows($products);

        return DB::transaction(function () use ($compra, $data, $products) {
            $this->revertStock($compra);
            $compra->detalles()->delete();

            if (empty($products)) {
                $compra->delete();
                return null;
            }

            $totals = $this->calculateTotals($products);
            $newProducts = [];
            $updatedProducts = [];

            $compra->update([
                'proveedor_id'   => $data['proveedor_id'],
                'almacen_id'     => $data['almacen_id'] ?? $compra->almacen_id,
                'tipo_compra_id' => $data['tipo_compra_id'],
                'total'          => $totals['total'],
                'itbis_total'    => $totals['itbis_total'],
                'subtotal'       => $totals['subtotal'],
                'fecha'          => $data['fecha'] ?? $compra->fecha ?? now(),
                'observaciones'  => $data['observaciones'] ?? null,
            ]);

            foreach ($products as $item) {
                $producto = $this->resolveOrCreateProduct($item, $newProducts, $updatedProducts);

                $detalle = DetalleCompra::create([
                    'compra_id'        => $compra->id,
                    'producto_id'      => $producto->id,
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio'],
                    'itbis_porcentaje' => $item['itbis_porcentaje'] ?? 18,
                    'subtotal'         => $this->computeDetailSubtotal($item),
                ]);

                $this->createInventoryMovement($compra, $detalle, $producto, $item['cantidad']);
            }

            $this->applyRetentions($compra, $data, $totals);

            $compra->setRelation('newProducts', $newProducts);
            $compra->setRelation('updatedProducts', $updatedProducts);

            return $compra;
        });
    }

    public function deletePurchase(Compra $compra): void
    {
        DB::transaction(function () use ($compra) {
            $this->revertStock($compra);
            $compra->detalles()->delete();
            $compra->delete();
        });
    }

    public function removeDetail(Compra $compra, DetalleCompra $detalle): void
    {
        DB::transaction(function () use ($compra, $detalle) {
            if ($detalle->producto) {
                $detalle->producto->decrement('stock', $detalle->cantidad);
            }
            $detalle->delete();
            $this->recalculateTotals($compra);
        });
    }

    public function buildSuccessMessage(Compra $compra, string $action = 'registrada'): string
    {
        $newProducts = $compra->getRelation('newProducts') ?? [];
        $updatedProducts = $compra->getRelation('updatedProducts') ?? [];

        $message = "Compra {$action} exitosamente.";

        if (!empty($newProducts)) {
            $links = array_map(fn($id) =>
                '<a href="' . route('productos.edit', $id) . '">Producto #' . $id . '</a>',
                $newProducts
            );
            $message .= ' Productos nuevos: ' . implode(', ', $links) . '.';

            if ($action === 'registrada') {
                $message .= ' <strong>Recuerda asignar el precio de venta.</strong>';
            }
        }

        if (!empty($updatedProducts)) {
            $message .= ' Stock actualizado en ' . count($updatedProducts) . ' producto(s).';
        }

        return $message;
    }

    public function filterEmptyRows(array $products): array
    {
        return collect($products)
            ->filter(function ($item) {
                $hasName = !empty(trim($item['nombre'] ?? ''));
                $hasProductId = !empty($item['producto_id']);
                $hasQuantity = isset($item['cantidad']) && (float) $item['cantidad'] > 0;
                $hasPrice = isset($item['precio']) && $item['precio'] !== '' && (float) $item['precio'] >= 0;
                return ($hasName || $hasProductId) && $hasQuantity && $hasPrice;
            })
            ->values()
            ->all();
    }

    public function calculateTotals(array $products): array
    {
        $subtotal = 0.0;
        $itbisTotal = 0.0;
        $total = 0.0;

        foreach ($products as $item) {
            $cantidad = (float) $item['cantidad'];
            $precio   = (float) $item['precio'];
            $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);
            $base     = $cantidad * $precio;
            $impuesto = $base * ($itbis / 100);
            $subtotal   += $base;
            $itbisTotal += $impuesto;
            $total      += $base + $impuesto;
        }

        return [
            'subtotal'    => round($subtotal, 2),
            'itbis_total' => round($itbisTotal, 2),
            'total'       => round($total, 2),
        ];
    }

    public function computeDetailSubtotal(array $item): float
    {
        $cantidad = (float) $item['cantidad'];
        $precio   = (float) $item['precio'];
        $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);
        return round($cantidad * $precio * (1 + $itbis / 100), 2);
    }

    public function resolveOrCreateProduct(array $item, array &$newProducts, array &$updatedProducts): Producto
    {
        $cantidad = (float) $item['cantidad'];
        $precio   = (float) $item['precio'];
        $itbis    = (float) ($item['itbis_porcentaje'] ?? 18);

        $producto = null;
        if (!empty($item['producto_id'])) {
            $producto = Producto::find($item['producto_id']);
        }

        if (! $producto && !empty($item['nombre'])) {
            $producto = Producto::where('nombre', trim($item['nombre']))->first();
        }

        if ($producto) {
            $producto->stock += $cantidad;
            if ((float) $producto->precio_compra != $precio) {
                $producto->precio_compra = $precio;
            }
            $producto->save();
            $updatedProducts[] = $producto->id;
            return $producto;
        }

        $producto = Producto::create([
            'nombre'           => trim($item['nombre']),
            'codigo_barras'    => !empty($item['codigo_barras']) ? trim($item['codigo_barras']) : null,
            'precio_compra'    => $precio,
            'precio'           => $precio,
            'stock'            => $cantidad,
            'itbis_porcentaje' => $itbis,
            'unidad_medida'    => 'Unidad',
        ]);
        $newProducts[] = $producto->id;
        return $producto;
    }

    public function recalculateTotals(Compra $compra): void
    {
        $detalles = $compra->detalles()->get();
        $totals = ['subtotal' => 0, 'itbis_total' => 0, 'total' => 0];

        foreach ($detalles as $d) {
            $base    = (float) $d->cantidad * (float) $d->precio_unitario;
            $itbis   = (float) ($d->itbis_porcentaje ?? 18);
            $impuesto = $base * ($itbis / 100);
            $totals['subtotal']   += $base;
            $totals['itbis_total'] += $impuesto;
            $totals['total']      += $base + $impuesto;
        }

        $updateData = [
            'subtotal'    => round($totals['subtotal'], 2),
            'itbis_total' => round($totals['itbis_total'], 2),
            'total'       => round($totals['total'], 2),
        ];

        if ($compra->aplica_retencion_isr || $compra->aplica_retencion_itbis) {
            $isr = $compra->aplica_retencion_isr
                ? RetencionCalculator::calcularRetencionIsr($updateData['total'], $compra->proveedor?->tipo_persona ?? 'juridica')['monto_retenido']
                : 0;
            $itbis = $compra->aplica_retencion_itbis
                ? RetencionCalculator::calcularRetencionItbis($updateData['itbis_total'])['monto_retenido']
                : 0;
            $updateData['retencion_isr'] = $isr;
            $updateData['retencion_itbis'] = $itbis;
            $updateData['total_neto'] = round($updateData['total'] - $isr - $itbis, 2);
        }

        $compra->update($updateData);
    }

    private function applyRetentions(Compra $compra, array $data, array $totals): void
    {
        $proveedor = Proveedor::find($data['proveedor_id']);
        $applyIsr = !empty($data['aplica_retencion_isr']) && $proveedor?->sujeto_retencion_isr;
        $applyItbis = !empty($data['aplica_retencion_itbis']) && $proveedor?->sujeto_retencion_itbis;

        $retencionIsr = $applyIsr
            ? RetencionCalculator::calcularRetencionIsr($totals['total'], $proveedor?->tipo_persona ?? 'juridica')['monto_retenido']
            : 0;

        $retencionItbis = $applyItbis
            ? RetencionCalculator::calcularRetencionItbis($totals['itbis_total'])['monto_retenido']
            : 0;

        $compra->update([
            'aplica_retencion_isr'   => $applyIsr,
            'aplica_retencion_itbis' => $applyItbis,
            'retencion_isr'          => $retencionIsr,
            'retencion_itbis'        => $retencionItbis,
            'total_neto'             => round($totals['total'] - $retencionIsr - $retencionItbis, 2),
        ]);
    }

    private function createInventoryMovement(Compra $compra, DetalleCompra $detalle, Producto $producto, float $cantidad): void
    {
        if (! $compra->almacen_id) {
            return;
        }

        AlmacenMovimiento::create([
            'producto_id'       => $producto->id,
            'detalle_compra_id'  => $detalle->id,
            'user_id'            => Auth::id(),
            'almacen_id'         => $compra->almacen_id,
            'tipo'               => 'entrada',
            'cantidad'           => $cantidad,
            'nota'               => "Entrada por compra #{$compra->id}",
        ]);
    }

    private function revertStock(Compra $compra): void
    {
        foreach ($compra->detalles as $detalle) {
            if ($detalle->producto) {
                $detalle->producto->decrement('stock', $detalle->cantidad);
            }
        }
    }
}
