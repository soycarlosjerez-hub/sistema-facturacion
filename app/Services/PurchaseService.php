<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Equipo;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\SystemSetting;
use App\Support\RetencionCalculator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class PurchaseService
{
    /**
     * Crea una compra adaptada al modo de facturación del negocio.
     *
     * @param array  $data              Datos del header de la compra
     * @param array  $products          Array de productos/equipos
     * @param string $facturacion_modo  'productos' | 'equipos' | 'obras_arte'
     * @return Compra
     */
    public function createPurchase(array $data, array $products, string $facturacion_modo = 'productos'): Compra
    {
        $products = $this->filterEmptyRows($products);
        $totals = $this->calculateTotals($products);

        $newProducts = [];
        $updatedProducts = [];

        return DB::transaction(function () use ($data, $products, $totals, &$newProducts, &$updatedProducts) {
            $compra = Compra::create([
                'tenant_id'       => Auth::user()->business_instance_id ?? null,
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

            // Si el modo es 'equipos', cada fila genera un Equipo individual
            if ($facturacion_modo === 'equipos') {
                foreach ($products as $item) {
                    $producto = $this->resolveOrCreateProduct($item, $newProducts, $updatedProducts);
                    $equipo = $this->crearEquipoDesdeCompra($item, $compra, $producto);

                    $detalle = DetalleCompra::create([
                        'compra_id'         => $compra->id,
                        'producto_id'       => $producto->id,
                        'equipo_id'         => $equipo->id,
                        'cantidad'          => 1, // Cada equipo es 1 unidad
                        'precio_unitario'   => $item['precio'],
                        'itbis_porcentaje'  => $item['itbis_porcentaje'] ?? SystemSetting::itbisDefault(),
                        'subtotal'          => $this->computeDetailSubtotal($item),
                        'tenant_id'         => Auth::user()->business_instance_id ?? null,
                    ]);
                }
            } else {
                // Modo normal: productos genéricos con stock
                foreach ($products as $item) {
                    $producto = $this->resolveOrCreateProduct($item, $newProducts, $updatedProducts);

                    $detalle = DetalleCompra::create([
                        'compra_id'         => $compra->id,
                        'producto_id'       => $producto->id,
                        'cantidad'          => $item['cantidad'],
                        'precio_unitario'   => $item['precio'],
                        'itbis_porcentaje'  => $item['itbis_porcentaje'] ?? SystemSetting::itbisDefault(),
                        'subtotal'          => $this->computeDetailSubtotal($item),
                        'tenant_id'         => Auth::user()->business_instance_id ?? null,
                    ]);

                    $this->createInventoryMovement($compra, $detalle, $producto, $item['cantidad']);
                }
            }

            $this->applyRetentions($compra, $data, $totals);

            $compra->setRelation('newProducts', $newProducts);
            $compra->setRelation('updatedProducts', $updatedProducts);

            return $compra;
        });
    }

    public function updatePurchase(Compra $compra, array $data, array $products, string $facturacion_modo = 'productos'): Compra
    {
        $products = $this->filterEmptyRows($products);

        return DB::transaction(function () use ($compra, $data, $products, $facturacion_modo) {
            // Revertir stock (modo productos) o liberar equipos (modo equipos)
            if ($facturacion_modo === 'equipos') {
                foreach ($compra->detalles as $detalle) {
                    if ($detalle->equipo) {
                        $detalle->equipo->update(['estado' => 'disponible']);
                    }
                }
            } else {
                $this->revertStock($compra);
            }
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

                if ($facturacion_modo === 'equipos') {
                    $equipo = $this->crearEquipoDesdeCompra($item, $compra, $producto);

                    DetalleCompra::create([
                        'compra_id'        => $compra->id,
                        'producto_id'      => $producto->id,
                        'equipo_id'        => $equipo->id,
                        'cantidad'         => 1,
                        'precio_unitario'  => $item['precio'],
                        'itbis_porcentaje' => $item['itbis_porcentaje'] ?? SystemSetting::itbisDefault(),
                        'subtotal'         => $this->computeDetailSubtotal($item),
                        'tenant_id'        => Auth::user()->business_instance_id ?? null,
                    ]);
                } else {
                    $detalle = DetalleCompra::create([
                        'compra_id'        => $compra->id,
                        'producto_id'      => $producto->id,
                        'cantidad'         => $item['cantidad'],
                        'precio_unitario'  => $item['precio'],
                        'itbis_porcentaje' => $item['itbis_porcentaje'] ?? SystemSetting::itbisDefault(),
                        'subtotal'         => $this->computeDetailSubtotal($item),
                        'tenant_id'        => Auth::user()->business_instance_id ?? null,
                    ]);

                    $this->createInventoryMovement($compra, $detalle, $producto, $item['cantidad']);
                }
            }

            $this->applyRetentions($compra, $data, $totals);

            $compra->setRelation('newProducts', $newProducts);
            $compra->setRelation('updatedProducts', $updatedProducts);

            return $compra;
        });
    }

    public function deletePurchase(Compra $compra, string $facturacion_modo = 'productos'): void
    {
        DB::transaction(function () use ($compra, $facturacion_modo) {
            if ($facturacion_modo === 'equipos') {
                foreach ($compra->detalles as $detalle) {
                    if ($detalle->equipo) {
                        // El equipo se libera a "disponible" (aunque en teoría ya no debería estar vendido)
                        $detalle->equipo->update(['estado' => 'disponible']);
                    }
                }
            } else {
                $this->revertStock($compra);
            }
            $compra->detalles()->delete();
            $compra->delete();
        });
    }

    public function removeDetail(Compra $compra, DetalleCompra $detalle, string $facturacion_modo = 'productos'): void
    {
        DB::transaction(function () use ($compra, $detalle, $facturacion_modo) {
            if ($facturacion_modo === 'equipos') {
                if ($detalle->equipo) {
                    // Liberar el equipo a "disponible"
                    $detalle->equipo->update(['estado' => 'disponible']);
                }
            } else {
                if ($detalle->producto) {
                    $detalle->producto->decrement('stock', $detalle->cantidad);
                    if ($detalle->producto->stock <= ($detalle->producto->stock_minimo ?? 5)) {
                        Event::dispatch(new \App\Events\StockCritical($detalle->producto, $detalle->producto->stock));
                    }
                }
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
            $itbis    = (float) ($item['itbis_porcentaje'] ?? SystemSetting::itbisDefault());
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
        $itbis    = (float) ($item['itbis_porcentaje'] ?? SystemSetting::itbisDefault());
        return round($cantidad * $precio * (1 + $itbis / 100), 2);
    }

    public function resolveOrCreateProduct(array $item, array &$newProducts, array &$updatedProducts): Producto
    {
        $cantidad = (float) $item['cantidad'];
        $precio   = (float) $item['precio'];
        $itbis    = (float) ($item['itbis_porcentaje'] ?? SystemSetting::itbisDefault());
        $precioVenta = ($item['precio_venta'] ?? null) !== '' && ($item['precio_venta'] ?? null) !== null
            ? (float) $item['precio_venta']
            : null;

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
            if ($precioVenta !== null && (float) $producto->precio != $precioVenta) {
                $producto->precio = $precioVenta;
            }
            $producto->save();
            $updatedProducts[] = $producto->id;
            return $producto;
        }

        $producto = Producto::create([
            'tenant_id'        => Auth::user()->business_instance_id ?? null,
            'nombre'           => trim($item['nombre']),
            'codigo_barras'    => !empty($item['codigo_barras']) ? trim($item['codigo_barras']) : null,
            'precio_compra'    => $precio,
            'precio'           => $precioVenta !== null ? $precioVenta : $precio,
            'stock'            => $cantidad,
            'itbis_porcentaje' => $itbis,
            'unidad_medida'    => 'Unidad',
        ]);
        $newProducts[] = $producto->id;
        return $producto;
    }

    /**
     * Crea un Equipo individual a partir de una fila de compra.
     * Solo se usa cuando el negocio tiene facturacion_modo='equipos'.
     *
     * @param array       $item       Datos del equipo (IMEI, marca, modelo, etc.)
     * @param Compra      $compra     La compra padre
     * @param Producto    $producto   Producto asociado (padre del equipo)
     * @return Equipo
     */
    private function crearEquipoDesdeCompra(array $item, Compra $compra, Producto $producto): Equipo
    {
        $imei = trim($item['serial_imei'] ?? $item['serial'] ?? '');
        if (empty($imei)) {
            throw new \InvalidArgumentException('El IMEI/Serial es obligatorio para equipos.');
        }

        $precioVenta = ($item['precio_venta'] ?? null) !== '' && ($item['precio_venta'] ?? null) !== null
            ? (float) $item['precio_venta']
            : null;

        // Buscar si ya existe un equipo con ese IMEI
        $equipo = Equipo::where('serial_imei', $imei)
                       ->where('tenant_id', Auth::user()->business_instance_id ?? null)
                       ->first();

        if ($equipo) {
            throw new \InvalidArgumentException('Ya existe un equipo con el IMEI/Serial: ' . $imei);
        }

        // Si el producto existe, verificar si el IMEI ya está registrado en otro tenant
        $equipoExistenteGlobal = Equipo::where('serial_imei', $imei)->first();
        if ($equipoExistenteGlobal) {
            throw new \InvalidArgumentException('El IMEI/Serial ' . $imei . ' ya fue registrado con anterioridad.');
        }

        $equipo = Equipo::create([
            'tenant_id'             => Auth::user()->business_instance_id ?? null,
            'user_id'               => Auth::id(),
            'producto_id'           => $producto->id,
            'serial_imei'           => $imei,
            'serial_esn'            => trim($item['serial_esn'] ?? ''),
            'marca'                 => trim($item['marca'] ?? $producto->nombre ?? ''),
            'modelo'                => trim($item['modelo'] ?? ''),
            'almacenamiento_gb'     => trim($item['almacenamiento_gb'] ?? ''),
            'color'                 => trim($item['color'] ?? ''),
            'estado'                => 'disponible',
            'precio_compra'         => $item['precio'] ?? 0,
            'precio_venta'          => $precioVenta ?? $item['precio'] ?? 0,
            'comprado_a_proveedor_id' => $compra->proveedor_id,
            'fecha_compra'          => $compra->fecha ?? now(),
            'factura_compra'        => trim($item['factura_compra'] ?? $compra->folio ?? ''),
            'garantia_desde'        => !empty($item['garantia_desde']) ? $item['garantia_desde'] : null,
            'garantia_hasta'        => !empty($item['garantia_hasta']) ? $item['garantia_hasta'] : null,
            'garantia_tipo'         => $item['garantia_tipo'] ?? 'fabrica',
            'bloqueado_icloud'      => !empty($item['bloqueado_icloud']),
            'bloqueado_fr'          => !empty($item['bloqueado_fr']),
            'observaciones'         => trim($item['observaciones'] ?? ''),
            'tipo_dispositivo'      => trim($item['tipo_dispositivo'] ?? 'celular'),
            'procesador'            => trim($item['procesador'] ?? ''),
            'memoria_ram'           => trim($item['memoria_ram'] ?? ''),
            'almacenamiento_tipo'   => trim($item['almacenamiento_tipo'] ?? ''),
            'almacenamiento_capacidad' => trim($item['almacenamiento_capacidad'] ?? ''),
            'sistema_operativo'     => trim($item['sistema_operativo'] ?? ''),
            'puertos'               => trim($item['puertos'] ?? ''),
            'peso_gramos'           => isset($item['peso_gramos']) ? (float) $item['peso_gramos'] : null,
        ]);

        return $equipo;
    }

    public function recalculateTotals(Compra $compra): void
    {
        $detalles = $compra->detalles()->get();
        $totals = ['subtotal' => 0, 'itbis_total' => 0, 'total' => 0];

        foreach ($detalles as $d) {
            $base    = (float) $d->cantidad * (float) $d->precio_unitario;
            $itbis   = (float) ($d->itbis_porcentaje ?? SystemSetting::itbisDefault());
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
            'tenant_id'          => Auth::user()->business_instance_id,
            'producto_id'        => $producto->id,
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
