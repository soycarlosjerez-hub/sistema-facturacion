<?php

namespace App\Services;

use App\Events\StockCritical;
use App\Models\AlmacenMovimiento;
use App\Models\ArteObra;
use App\Models\Equipo;
use App\Models\EquipoVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

/**
 * Servicio especializado en gestión de inventario y stock de ventas.
 * 
 * Responsable: reducir stock, registrar movimientos de almacén,
 * marcar obras como vendidas, registrar ventas de equipos,
 * verificar stock disponible.
 * 
 * Se encarga de las consecuencias de inventario de una venta,
 * separando la lógica de persistencia de datos de la lógica de cálculo.
 */
class SaleStockService
{
    /**
     * Validar y reducir stock para una línea de producto normal.
     * 
     * @param Venta $venta La venta actual
     * @param VentaDetalle|null $ventaExistente Venta anterior para adiciones de cuenta abierta
     * @param int $productoId ID del producto
     * @param int $cantidad Cantidad a vender
     * @param int|null $almacenId ID del almacén (null = almacén por defecto)
     * @param SaleCalcService $calcService Servicio de cálculos
     * @param bool $validarStock Si debe verificar stock antes de vender
     * @return Producto El producto actualizado
     * @throws \Exception Si el stock es insuficiente
     */
    public function reducirStockProducto(
        Venta $venta,
        ?Venta $ventaExistente,
        int $productoId,
        int $cantidad,
        ?int $almacenId,
        SaleCalcService $calcService,
        bool $validarStock = true
    ): Producto {
        $producto = Producto::findOrFail($productoId);

        if ($validarStock) {
            $disponiblePorAlmacen = $almacenId
                ? $this->checkStock($productoId, $almacenId)
                : $producto->stock;

            if ($disponiblePorAlmacen === 0 && $almacenId) {
                $disponiblePorAlmacen = max($disponiblePorAlmacen, $producto->stock);
            }

            if ($disponiblePorAlmacen < $cantidad || $producto->stock < $cantidad) {
                throw new \Exception(
                    "Stock insuficiente para: {$producto->nombre} " .
                    "(Disponible en almacén: {$disponiblePorAlmacen}, " .
                    "Stock global: {$producto->stock})"
                );
            }
        }

        // Registrar movimiento de almacén (salida)
        AlmacenMovimiento::create([
            'tenant_id'   => Auth::user()->business_instance_id,
            'producto_id' => $productoId,
            'almacen_id'  => $almacenId,
            'tipo'        => 'salida',
            'cantidad'    => $cantidad,
            'nota'        => 'Venta #' . $venta->id . ($ventaExistente ? ' (Adición)' : ''),
            'user_id'     => Auth::id(),
        ]);

        // Decrementar stock global
        $producto->decrement('stock', $cantidad);

        // Disparar evento de stock crítico
        if ($producto->stock <= ($producto->stock_minimo ?? 5)) {
            Event::dispatch(new StockCritical($producto, $producto->stock));
        }

        // Incrementar contador de ventas
        $producto->increment('ventas_count', $cantidad);

        return $producto;
    }

    /**
     * Marcar una obra de arte como vendida y registrar detalle.
     * 
     * @param Venta $venta La venta actual
     * @param int $obraId ID de la obra
     * @param float $precio Precio de venta
     * @param float $subtotal Subtotal
     * @param float $descuento Descuento aplicado
     * @param string $descuentoTipo Tipo de descuento ('monto' o 'porcentaje')
     * @param float $itbisPorcentaje Porcentaje de ITBIS
     * @param bool $sinItbis Si la obra no tiene ITBIS
     * @param int $tenantId ID de la instancia
     * @return VentaDetalle El detalle creado
     * @throws \Exception Si la obra no existe o ya fue vendida
     */
    public function marcarObraVendida(
        Venta $venta,
        int $obraId,
        float $precio,
        float $subtotal,
        float $descuento,
        string $descuentoTipo,
        float $itbisPorcentaje,
        bool $sinItbis,
        int $tenantId
    ): VentaDetalle {
        $obra = ArteObra::where('tenant_id', $tenantId)->find($obraId);

        if (!$obra) {
            throw new \Exception('La obra #' . $obraId . ' no existe.');
        }

        if ($obra->estado === 'vendida') {
            throw new \Exception("La obra \"{$obra->titulo}\" ya fue vendida.");
        }

        $detalle = VentaDetalle::create([
            'venta_id'         => $venta->id,
            'obra_id'          => $obra->id,
            'cantidad'         => 1,
            'precio_unitario'  => $precio,
            'subtotal'         => $subtotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $descuentoTipo,
            'itbis_porcentaje' => $itbisPorcentaje,
            'sin_itbis'        => $sinItbis,
            'tenant_id'        => $tenantId,
        ]);

        $obra->update(['estado' => 'vendida']);

        return $detalle;
    }

    /**
     * Registrar la venta de un equipo (dispositivo con IMEI/Serial).
     * 
     * @param Venta $venta La venta actual
     * @param int $equipoId ID del equipo
     * @param float $precio Precio de venta
     * @param float $subtotal Subtotal
     * @param float $descuento Descuento aplicado
     * @param string $descuentoTipo Tipo de descuento
     * @param float $itbisPorcentaje Porcentaje de ITBIS
     * @param bool $sinItbis Si no aplica ITBIS
     * @param int $tenantId ID de la instancia
     * @return array ['detalle' => VentaDetalle, 'equipoVenta' => EquipoVenta]
     * @throws \Exception Si el equipo no existe o no está disponible
     */
    public function registrarVentaEquipo(
        Venta $venta,
        int $equipoId,
        float $precio,
        float $subtotal,
        float $descuento,
        string $descuentoTipo,
        float $itbisPorcentaje,
        bool $sinItbis,
        int $tenantId
    ): array {
        $equipo = Equipo::find($equipoId);

        if (!$equipo) {
            throw new \Exception('El equipo #' . $equipoId . ' no existe.');
        }

        if ($equipo->estado !== 'disponible') {
            throw new \Exception('El equipo ' . $equipo->serial_imei . ' no está disponible para venta.');
        }

        $detalle = VentaDetalle::create([
            'venta_id'         => $venta->id,
            'equipo_id'        => $equipoId,
            'producto_id'      => $equipo->producto_id ?? null,
            'cantidad'         => 1,
            'precio_unitario'  => $precio,
            'subtotal'         => $subtotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $descuentoTipo,
            'itbis_porcentaje' => $itbisPorcentaje,
            'sin_itbis'        => $sinItbis,
            'almacen_id'       => null,
            'tenant_id'        => $tenantId,
        ]);

        $equipoVenta = EquipoVenta::create([
            'equipo_id'        => $equipoId,
            'venta_id'         => $venta->id,
            'precio_vendido'   => $precio,
            'tenant_id'        => $tenantId,
        ]);

        $equipo->update(['estado' => 'vendido']);

        return [
            'detalle'      => $detalle,
            'equipoVenta'  => $equipoVenta,
        ];
    }

    /**
     * Devolver stock al anular una venta (restaurar entradas de almacén).
     * 
     * @param Venta $venta La venta a anular
     * @param string $motivo Motivo de la anulación
     * @param int $tenantId ID de la instancia
     */
    public function devolverStock(Venta $venta, string $motivo, int $tenantId): void
    {
        $stockUpdates = [];

        foreach ($venta->detalles as $detalle) {
            $almacenId = ($detalle->almacen_id > 0) ? $detalle->almacen_id : null;

            if ($almacenId) {
                AlmacenMovimiento::create([
                    'tenant_id'   => $tenantId,
                    'producto_id' => $detalle->producto_id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'entrada',
                    'cantidad'    => $detalle->cantidad,
                    'nota'        => 'ANULACIÓN Venta #' . $venta->id . ' | Motivo: ' . $motivo,
                    'user_id'     => Auth::id(),
                ]);
            }

            $stockUpdates[$detalle->producto_id] = ($stockUpdates[$detalle->producto_id] ?? 0) + $detalle->cantidad;
        }

        if (!empty($stockUpdates)) {
            foreach ($stockUpdates as $productId => $qty) {
                Producto::where('id', $productId)->increment('stock', $qty);
            }
        }
    }

    /**
     * Revertir una obra de arte de vuelta a 'disponible'.
     * 
     * @param Venta $venta La venta a anular
     * @param int $tenantId ID de la instancia
     */
    public function revertirObrasVendidas(Venta $venta, int $tenantId): void
    {
        foreach ($venta->detalles->whereNotNull('obra_id') as $detalle) {
            $obra = ArteObra::where('tenant_id', $tenantId)->find($detalle->obra_id);
            if ($obra && $obra->estado === 'vendida') {
                $obra->update(['estado' => 'disponible']);
            }
        }
    }

    /**
     * Revertir un equipo de vuelta a 'disponible' y eliminar registro de EquipoVenta.
     * 
     * @param Venta $venta La venta a anular
     */
    public function revertirEquiposVendidos(Venta $venta): void
    {
        foreach ($venta->detalles->whereNotNull('equipo_id') as $detalle) {
            $equipo = Equipo::find($detalle->equipo_id);
            if ($equipo && $equipo->estado === 'vendido') {
                $equipo->update(['estado' => 'disponible']);
            }

            EquipoVenta::where('equipo_id', $detalle->equipo_id)
                      ->where('venta_id', $venta->id)
                      ->delete();
        }
    }

    /**
     * Verificar stock disponible en un almacén específico.
     * 
     * @param int $productoId ID del producto
     * @param int $almacenId ID del almacén
     * @return int Stock disponible
     */
    public function checkStock(int $productoId, int $almacenId): int
    {
        $stock = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->selectRaw('SUM(CASE WHEN tipo = "entrada" THEN cantidad ELSE -cantidad END) as stock')
            ->value('stock') ?? 0;

        return (int) $stock;
    }

    /**
     * Verificar stock global de un producto.
     * 
     * @param int $productoId ID del producto
     * @return int Stock global
     */
    public function checkStockGlobal(int $productoId): int
    {
        $producto = Producto::find($productoId);
        return $producto ? (int) $producto->stock : 0;
    }

    /**
     * Verificar stock para múltiples productos (batch).
     * 
     * @param array $productoIds Array de IDs de productos
     * @param int|null $almacenId ID del almacén (null = verificar global)
     * @return array ['producto_id' => stock]
     */
    public function checkStockMultiple(array $productoIds, ?int $almacenId = null): array
    {
        $stocks = [];

        foreach ($productoIds as $productoId) {
            if ($almacenId) {
                $stocks[$productoId] = $this->checkStock($productoId, $almacenId);
            } else {
                $stocks[$productoId] = $this->checkStockGlobal($productoId);
            }
        }

        return $stocks;
    }

    /**
     * Registrar servicio de lavado como detalle de venta.
     * 
     * @param Venta $venta La venta actual
     * @param int $servicioId ID del servicio
     * @param int $cantidad Cantidad
     * @param float $precio Precio unitario
     * @param float $subtotal Subtotal
     * @param float $descuento Descuento
     * @param string $descuentoTipo Tipo de descuento
     * @param float $itbisPorcentaje Porcentaje ITBIS
     * @param bool $sinItbis Sin ITBIS
     * @param int $tenantId ID de la instancia
     * @return VentaDetalle El detalle creado
     */
    public function registrarServicioLavado(
        Venta $venta,
        int $servicioId,
        int $cantidad,
        float $precio,
        float $subtotal,
        float $descuento,
        string $descuentoTipo,
        float $itbisPorcentaje,
        bool $sinItbis,
        int $tenantId
    ): VentaDetalle {
        return VentaDetalle::create([
            'venta_id'         => $venta->id,
            'servicio_id'      => $servicioId,
            'cantidad'         => $cantidad,
            'precio_unitario'  => $precio,
            'subtotal'         => $subtotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $descuentoTipo,
            'itbis_porcentaje' => $itbisPorcentaje,
            'sin_itbis'        => $sinItbis,
            'almacen_id'       => null,
            'tenant_id'        => $tenantId,
        ]);
    }
}
