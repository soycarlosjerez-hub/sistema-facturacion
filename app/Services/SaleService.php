<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
use App\Models\ArteObra;
use App\Models\Category;
use App\Models\Cliente;
use App\Models\DeliveryCompany;
use App\Models\DeliveryTracking;
use App\Models\EcfDocumento;
use App\Models\Equipo;
use App\Models\EquipoVenta;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Services\Ecf\EcfService;
use App\Support\RncValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class SaleService
{
    protected NcfService $ncfService;

    protected EcfService $ecfService;

    protected RetentionService $retentionService;
    protected SaleCreateService $createService;

    public function __construct(NcfService $ncfService, EcfService $ecfService, RetentionService $retentionService, SaleCreateService $createService)
    {
        $this->ncfService = $ncfService;
        $this->ecfService = $ecfService;
        $this->retentionService = $retentionService;
        $this->createService = $createService;
    }


    /**
     * Delegar la creación de venta al servicio especializado.
     * Mantiene compatibilidad con VentaController que depende de este método.
     */
    public function createSale(array $data, SesionCaja $sesion): Venta
    {
        return $this->createService->createSale($data, $sesion);
    }


    /**
     * Delegar getCreationData al servicio especializado.
     * Mantiene compatibilidad con VentaController.
     */
    public function getCreationData(): array
    {
        return $this->createService->getCreationData();
    }

    public function checkStock(int $productoId, int $almacenId): int
    {
        $stock = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->selectRaw('SUM(CASE WHEN tipo = "entrada" THEN cantidad ELSE -cantidad END) as stock')
            ->value('stock') ?? 0;

        return (int) $stock;
    }

    public function procesarEcf(Venta $venta): void
    {
        // Idempotente (F0.4): reutilizar el e-CF existente si ya se emitió uno
        $existente = EcfDocumento::where('venta_id', $venta->id)
            ->whereNotNull('encf')
            ->orderByDesc('id')
            ->first();

        if ($existente) {
            if ($existente->pendienteEnvio()) {
                try {
                    $this->ecfService->enviar($existente);
                } catch (\Throwable $e) {
                    Log::warning('No se pudo reenviar e-CF de la venta #'.$venta->id.': '.$e->getMessage());
                }
            }

            return;
        }

        if ($venta->cliente_id) {
            $cliente = $venta->cliente;
            if ($cliente && ! empty($cliente->rnc_cedula)) {
                $tipoDoc = $cliente->tipo_documento ?? RncValidator::inferirTipo($cliente->rnc_cedula);
                if (! RncValidator::validar($cliente->rnc_cedula, $tipoDoc)) {
                    throw new \Exception("El RNC/Cédula del cliente ({$cliente->rnc_cedula}) no es válido según DGII.");
                }
            } elseif ($cliente && in_array($venta->tipo_ecf ?? '', ['E31', 'E44', 'E45'])) {
                throw new \Exception('Los e-CF tipo Crédito Fiscal requieren un cliente con RNC válido.');
            }
        }
        try {
            $ecf = $this->ecfService->generarEcf($venta);
            $ecfFirmado = $this->ecfService->firmar($ecf);
            $this->ecfService->enviar($ecfFirmado);
        } catch (\Throwable $e) {
            Log::warning('No se pudo generar e-CF para la venta #'.$venta->id.': '.$e->getMessage());
        }
    }

    public function cancelSale(int $id, string $motivo): void
    {
        $venta = Venta::with(['ventaDetalles', 'ecfDocumento'])->findOrFail($id);

        if ($venta->estado === 'anulada') {
            throw new \Exception('Esta venta ya está anulada.');
        }

        // Si tiene e-CF emitido, anularlo
        if ($venta->ecfDocumento && $venta->ecfDocumento->encf) {
            $this->ecfService->anular($venta->ecfDocumento, $motivo);
        }

        // Soft delete detalles
        foreach ($venta->ventaDetalles as $detalle) {
            $detalle->delete();
        }

        // Soft delete la venta
        $venta->delete();
    }

    private function procesarDetalles(Venta $venta, array $data, ?Venta $ventaExistente): void
    {
        $tenantId = Auth::user()->business_instance_id;
        $modoObras = $this->facturaObrasArte();
        $modoProductosYServicios = $this->facturaProductosYServicios();

        // Ensure we always have a fallback almacen for the FK constraint
        $fallbackAlmacen = \App\Models\Almacen::where('tenant_id', $tenantId)->first();

        if ($modoObras) {
            $this->procesarDetallesObras($venta, $data, $tenantId);

            return;
        }

        if (! empty($data['equipo_id'] ?? [])) {
            $this->procesarDetallesEquipos($venta, $data, $tenantId);

            return;
        }

        if ($this->facturaProductosYServicios()) {
            $this->procesarDetallesMixto($venta, $data, $ventaExistente);

            return;
        }

        $productoIds = $data['producto_id'] ?? [];
        $cantidades = $data['cantidad'] ?? [];
        $precios = $data['precio'] ?? [];
        $subtotales = $data['subtotal'] ?? [];
        $almacenes = $data['almacen_id'] ?? [];

        $maxItems = count($productoIds);
        for ($i = 0; $i < $maxItems; $i++) {
            $productoId = $productoIds[$i] ?? null;
            if (! $productoId) {
                continue;
            }

            $cantidad = $cantidades[$i] ?? 0;
            $precio = $precios[$i] ?? 0;
            $subtotal = $subtotales[$i] ?? 0;
            $almacenId = isset($almacenes[$i]) && (int) $almacenes[$i] > 0
                ? (int) $almacenes[$i]
                : ($fallbackAlmacen?->id);
            $descuento = (float) ($data['descuento'][$i] ?? 0);
            $descuentoTipo = $data['descuento_tipo'][$i] ?? 'monto';
            $itbisPorcentaje = (float) ($data['itbis_porcentaje'][$i] ?? 0);

            $producto = Producto::findOrFail($productoId);

            if ($this->validaStock()) {
                $disponiblePorAlmacen = $almacenId ? $this->checkStock($productoId, $almacenId) : $producto->stock;
                if ($disponiblePorAlmacen < 0 && $almacenId) {
                    $disponiblePorAlmacen = $producto->stock;
                }
                if ($disponiblePorAlmacen < 0 && $almacenId) {
                    $disponiblePorAlmacen = $producto->stock;
                }
                if ($disponiblePorAlmacen === 0 && $almacenId) {
                    $disponiblePorAlmacen = max($disponiblePorAlmacen, $producto->stock);
                }
                if ($disponiblePorAlmacen < $cantidad || $producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre} (Disponible en almacén: {$disponiblePorAlmacen}, Stock global: {$producto->stock})");
                }
            }

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'descuento_tipo' => $descuentoTipo,
                'itbis_porcentaje' => $itbisPorcentaje,
                'sin_itbis' => (bool) ($data['sin_itbis'][$i] ?? false),
                'notas' => ($data['notas'][$i] ?? '') ?: null,
                'almacen_id' => $almacenId,
                'tenant_id' => $tenantId,
            ]);

            if ($this->validaStock()) {
                AlmacenMovimiento::create([
                    'tenant_id' => $tenantId,
                    'producto_id' => $productoId,
                    'almacen_id' => $almacenId,
                    'tipo' => 'salida',
                    'cantidad' => $cantidad,
                    'nota' => 'Venta #'.$venta->id.($ventaExistente ? ' (Adición)' : ''),
                    'user_id' => Auth::id(),
                ]);

                $producto->decrement('stock', $cantidad);

                if ($producto->stock <= ($producto->stock_minimo ?? 5)) {
                    Event::dispatch(new \App\Events\StockCritical($producto, $producto->stock));
                }
            }

            $producto->increment('ventas_count', $cantidad);
        }
    }

    private function procesarDetallesObras(Venta $venta, array $data, int $tenantId): void
    {
        $obraIds = $data['obra_id'] ?? [];
        $precios = $data['precio'] ?? [];
        $subtotales = $data['subtotal'] ?? [];
        $descuentos = $data['descuento'] ?? [];
        $descuentoTipos = $data['descuento_tipo'] ?? [];
        $itbisPorcentajes = $data['itbis_porcentaje'] ?? [];

        foreach ($obraIds as $i => $obraId) {
            if (! $obraId) {
                continue;
            }

            $obra = ArteObra::where('tenant_id', $tenantId)->find($obraId);
            if (! $obra) {
                throw new \Exception('La obra #'.$obraId.' no existe.');
            }
            if ($obra->estado === 'vendida') {
                throw new \Exception("La obra \"{$obra->titulo}\" ya fue vendida.");
            }

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'obra_id' => $obra->id,
                'cantidad' => 1,
                'precio_unitario' => $precios[$i] ?? $obra->precio_venta,
                'subtotal' => $subtotales[$i] ?? $obra->precio_venta,
                'descuento' => (float) ($descuentos[$i] ?? 0),
                'descuento_tipo' => $descuentoTipos[$i] ?? 'monto',
                'itbis_porcentaje' => (float) ($itbisPorcentajes[$i] ?? 0),
                'sin_itbis' => (bool) ($data['sin_itbis'][$i] ?? false),
                'notas' => $data['notas'][$i] ?? null,
                'tenant_id' => $tenantId,
            ]);

            $obra->update(['estado' => 'vendida']);
        }
    }

    private function procesarDetallesEquipos(Venta $venta, array $data, int $tenantId): void
    {
        $equipoIds = $data['equipo_id'] ?? [];
        $precios = $data['precio'] ?? [];
        $subtotales = $data['subtotal'] ?? [];
        $descuentos = $data['descuento'] ?? [];
        $descuentoTipos = $data['descuento_tipo'] ?? [];
        $itbisPorcentajes = $data['itbis_porcentaje'] ?? [];

        foreach ($equipoIds as $i => $equipoId) {
            if (! $equipoId) {
                continue;
            }

            $equipo = Equipo::find($equipoId);
            if (! $equipo) {
                throw new \Exception('El equipo #'.$equipoId.' no existe.');
            }
            if ($equipo->estado !== 'disponible') {
                throw new \Exception('El equipo '.$equipo->serial_imei.' no est\u00e1 disponible para venta.');
            }

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'equipo_id' => $equipoId,
                'producto_id' => $equipo->producto_id ?? null,
                'cantidad' => 1,
                'precio_unitario' => $precios[$i] ?? (float) $equipo->precio_venta,
                'subtotal' => $subtotales[$i] ?? (float) $equipo->precio_venta,
                'descuento' => (float) ($descuentos[$i] ?? 0),
                'descuento_tipo' => $descuentoTipos[$i] ?? 'monto',
                'itbis_porcentaje' => (float) ($itbisPorcentajes[$i] ?? 0),
                'sin_itbis' => (bool) ($data['sin_itbis'][$i] ?? false),
                'notas' => $data['notas'][$i] ?? null,
                'almacen_id' => null,
                'tenant_id' => $tenantId,
            ]);

            EquipoVenta::create([
                'equipo_id' => $equipoId,
                'venta_id' => $venta->id,
                'precio_vendido' => $precios[$i] ?? (float) $equipo->precio_venta,
                'tenant_id' => $tenantId,
            ]);

            $equipo->update(['estado' => 'vendido']);

            // Auto-create warranty for sold equipment
            /** @var \App\Models\Producto|null $producto */
            $producto = $equipo->producto;
            $garantiaDias = $producto?->garantia_dias ?? 90;

            // Use equipment factory warranty dates if available, otherwise calculate from product default
            if ($equipo->garantia_desde && $equipo->garantia_hasta) {
                $garantiaDesde = $equipo->garantia_desde;
                $garantiaHasta = $equipo->garantia_hasta;
                $garantiaTipo = $equipo->garantia_tipo ?? 'fabrica';
            } else {
                $garantiaDesde = $venta->fecha ?? now();
                $garantiaHasta = $garantiaDesde->copy()->addDays($garantiaDias);
                $garantiaTipo = 'fabrica';
            }

            // Try to apply GarantiasConfig rules if no factory dates
            if (! $equipo->garantia_desde && ! $equipo->garantia_hasta && $producto) {
                $config = \App\Models\GarantiasConfig::where('activo', true)
                    ->where(function ($q) use ($producto) {
                        $q->whereNull('tipo_producto')
                            ->orWhere('tipo_producto', $producto->tipo_producto);
                    })
                    ->orderBy('orden', 'asc')
                    ->first();

                if ($config) {
                    $garantiaDias = $config->dias_garantia;
                    $garantiaDesde = $venta->fecha ?? now();
                    $garantiaHasta = $garantiaDesde->copy()->addDays($garantiaDias);
                    $garantiaTipo = $config->tipo_garantia ?? 'fabrica';
                }
            }

            // --- Resolve warranty terms with fallback chain ---
            $terminosCondicion = null;

            if ($producto && ! empty($producto->garantia_terminos)) {
                $terminosCondicion = $producto->garantia_terminos;
            }
            if (empty($terminosCondicion) && isset($config) && ! empty($config->terminos_por_defecto)) {
                $terminosCondicion = $config->terminos_por_defecto;
            }

            // Create the Garantia record if we have a valid end date
            if ($garantiaHasta) {
                \App\Models\Garantia::create([
                    'venta_id' => $venta->id,
                    'equipo_id' => $equipoId,
                    'orden_reparacion_id' => null,
                    'tipo' => $garantiaTipo,
                    'fecha_inicio' => $garantiaDesde,
                    'fecha_fin' => $garantiaHasta,
                    'cobertura' => 100.00,
                    'estado' => 'vigente',
                    'terminos_condiciones' => $terminosCondicion,
                    'tenant_id' => $tenantId,
                ]);
            }
        }
    }

    private function procesarDetallesMixto(Venta $venta, array $data, ?Venta $ventaExistente): void
    {
        $tenantId = $venta->tenant_id;

        // Ensure we always have a fallback almacen for the FK constraint
        $fallbackAlmacen = \App\Models\Almacen::where('tenant_id', $tenantId)->first();

        // Procesar productos normales
        $productoIds = $data['producto_id'] ?? [];
        $cantidades = $data['cantidad'] ?? [];
        $precios = $data['precio'] ?? [];
        $subtotales = $data['subtotal'] ?? [];
        $almacenes = $data['almacen_id'] ?? [];
        $descuentos = $data['descuento'] ?? [];
        $descuentoTipos = $data['descuento_tipo'] ?? [];
        $itbisPorcentajes = $data['itbis_porcentaje'] ?? [];
        $sinItbis = $data['sin_itbis'] ?? [];
        $notasItems = $data['notas'] ?? [];
        // Asegurar que haya tantos elementos de notas como productos (relleno con vacío si faltan)
        $notasItems = array_pad($notasItems, count($productoIds), '');

        $maxItems = count($productoIds);
        for ($i = 0; $i < $maxItems; $i++) {
            $productoId = $productoIds[$i] ?? null;
            if (! $productoId) {
                continue;
            }

            $cantidad = $cantidades[$i] ?? 0;
            $precio = $precios[$i] ?? 0;
            $subtotal = $subtotales[$i] ?? 0;
            $almacenId = isset($almacenes[$i]) && (int) $almacenes[$i] > 0
                ? (int) $almacenes[$i]
                : ($fallbackAlmacen?->id);
            $descuento = (float) ($descuentos[$i] ?? 0);
            $descuentoTipo = $descuentoTipos[$i] ?? 'monto';
            $itbisPorcentaje = (float) ($itbisPorcentajes[$i] ?? 0);
            $sinItbisFlag = (bool) ($data['sin_itbis'][$i] ?? false);
            $itemNotas = ($notasItems[$i] ?? '') ?: null;

            $producto = Producto::findOrFail($productoId);

            if ($this->validaStock()) {
                $disponiblePorAlmacen = $almacenId ? $this->checkStock($productoId, $almacenId) : $producto->stock;
                if ($disponiblePorAlmacen < 0 && $almacenId) {
                    $disponiblePorAlmacen = $producto->stock;
                }
                if ($disponiblePorAlmacen === 0 && $almacenId) {
                    $disponiblePorAlmacen = max($disponiblePorAlmacen, $producto->stock);
                }
                if ($disponiblePorAlmacen < $cantidad || $producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre} (Disponible en almacén: {$disponiblePorAlmacen}, Stock global: {$producto->stock})");
                }
            }

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'descuento_tipo' => $descuentoTipo,
                'itbis_porcentaje' => $itbisPorcentaje,
                'sin_itbis' => $sinItbisFlag,
                'notas' => $itemNotas,
                'almacen_id' => $almacenId,
                'tenant_id' => $tenantId,
            ]);

            if ($this->validaStock()) {
                AlmacenMovimiento::create([
                    'tenant_id' => $tenantId,
                    'producto_id' => $productoId,
                    'almacen_id' => $almacenId,
                    'tipo' => 'salida',
                    'cantidad' => $cantidad,
                    'nota' => 'Venta #'.$venta->id.($ventaExistente ? ' (Adición)' : ''),
                    'user_id' => Auth::id(),
                ]);

                $producto->decrement('stock', $cantidad);

                if ($producto->stock <= ($producto->stock_minimo ?? 5)) {
                    Event::dispatch(new \App\Events\StockCritical($producto, $producto->stock));
                }
            }

            $producto->increment('ventas_count', $cantidad);
        }

        // Procesar servicios de lavado
        $servicioIds = $data['servicio_id'] ?? [];
        $cantidadesServ = $data['cantidad_servicio'] ?? [];
        $preciosServ = $data['precio_servicio'] ?? [];
        $subtotalesServ = $data['subtotal_servicio'] ?? [];
        $descuentosServ = $data['descuento_servicio'] ?? [];
        $descuentoTiposServ = $data['descuento_tipo_servicio'] ?? [];
        $itbisPorcentajesServ = $data['itbis_porcentaje_servicio'] ?? [];
        $sinItbisServ = $data['sin_itbis_servicio'] ?? [];

        foreach ($servicioIds as $i => $servicioId) {
            if (! $servicioId) {
                continue;
            }

            $servicio = \App\Models\LavaderoServicio::where('tenant_id', $tenantId)->find($servicioId);
            if (! $servicio) {
                throw new \Exception('El servicio de lavado #'.$servicioId.' no existe.');
            }

            VentaDetalle::create([
                'venta_id' => $venta->id,
                'servicio_id' => $servicioId,
                'cantidad' => $cantidadesServ[$i] ?? 1,
                'precio_unitario' => $preciosServ[$i] ?? $servicio->precio,
                'subtotal' => $subtotalesServ[$i] ?? $servicio->precio,
                'descuento' => (float) ($data['descuento_servicio'][$i] ?? 0),
                'descuento_tipo' => $descuentoTiposServ[$i] ?? 'monto',
                'itbis_porcentaje' => (float) ($data['itbis_porcentaje_servicio'][$i] ?? $servicio->itbis_porcentaje ?? $this->itbisPorcentajeInstancia()),
                'sin_itbis' => (bool) ($data['sin_itbis_servicio'][$i] ?? false),
                'almacen_id' => null,
                'tenant_id' => $tenantId,
            ]);
        }
    }

    private function procesarPago(Venta $venta, SesionCaja $sesion, string $metodo, string $estado, array $data): void
    {
        if (in_array($estado, ['pendiente', 'cuenta_abierta'])) {
            $cliente = Cliente::find($data['cliente_id']);
            if ($cliente && $cliente->nombre !== 'Consumidor Final') {
                $cliente->increment('balance_pendiente', $data['total']);
            }

            return;
        }

        if ($metodo === 'mixto') {
            $mixtoEfectivo = (float) ($data['mixto_efectivo'] ?? 0);
            $mixtoTarjeta = (float) ($data['mixto_tarjeta'] ?? 0);
            $mixtoTransferencia = (float) ($data['mixto_transferencia'] ?? 0);
            $mixtoSum = $mixtoEfectivo + $mixtoTarjeta + $mixtoTransferencia;
            if (abs($mixtoSum - (float) ($data['total'] ?? 0)) > 0.02) {
                throw new \Exception('La suma de los montos mixtos (RD$ '.number_format($mixtoSum, 2).') debe ser igual al total (RD$ '.number_format($data['total'], 2).').');
            }
            $mixtos = [
                'efectivo' => $mixtoEfectivo,
                'tarjeta' => $mixtoTarjeta,
                'transferencia' => $mixtoTransferencia,
            ];
            foreach ($mixtos as $tipo => $monto) {
                if ($monto > 0) {
                    Pago::create([
                        'tenant_id' => Auth::user()->business_instance_id,
                        'venta_id' => $venta->id,
                        'caja_id' => $sesion->caja_id,
                        'sesion_caja_id' => $sesion->id,
                        'monto' => $monto,
                        'metodo_pago' => $tipo,
                        'nota' => 'Pago mixto ('.ucfirst($tipo).')',
                        'fecha_pago' => now(),
                    ]);
                    match ($tipo) {
                        'efectivo' => $sesion->increment('ventas_efectivo', $monto),
                        'tarjeta' => $sesion->increment('ventas_tarjeta', $monto),
                        'transferencia' => $sesion->increment('ventas_transferencia', $monto),
                        default => null,
                    };
                }
            }

            return;
        }

        $pago = Pago::create([
            'tenant_id' => Auth::user()->business_instance_id,
            'venta_id' => $venta->id,
            'caja_id' => $sesion->caja_id,
            'sesion_caja_id' => $sesion->id,
            'monto' => $data['total'],
            'metodo_pago' => $metodo,
            'nota' => 'Pago automático (Venta '.ucfirst($metodo).')',
            'fecha_pago' => now(),
        ]);

        match ($metodo) {
            'efectivo' => $sesion->increment('ventas_efectivo', $data['total']),
            'tarjeta' => $sesion->increment('ventas_tarjeta', $data['total']),
            'transferencia' => $sesion->increment('ventas_transferencia', $data['total']),
            default => null,
        };

        Event::dispatch(new \App\Events\PaymentReceived($pago));
    }
}
