<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\EcfDocumento;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Support\RncValidator;
use App\Services\Ecf\EcfService;
use App\Services\NcfService;
use App\Services\RetentionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class SaleService
{
    protected NcfService $ncfService;
    protected EcfService $ecfService;
    protected RetentionService $retentionService;

    public function __construct(NcfService $ncfService, EcfService $ecfService, RetentionService $retentionService)
    {
        $this->ncfService = $ncfService;
        $this->ecfService = $ecfService;
        $this->retentionService = $retentionService;
    }

    public function createSale(array $data, SesionCaja $sesion): Venta
    {
        if (empty($data['cliente_id'])) {
            $consumidorFinal = Cliente::consumidorFinal(Auth::user()->business_instance_id);
            $data['cliente_id'] = $consumidorFinal->id;
        }

        $metodo = $data['metodo_pago'] ?? 'efectivo';
        $estado = match ($metodo) {
            'fiado' => 'pendiente',
            'cuenta_abierta' => 'cuenta_abierta',
            default => 'completada',
        };

        // --- Cálculo autoritativo server-side (F0.2/F0.3) ---
        $rolesAutorizados = ['admin', 'admin-business', 'root', 'gerente'];
        $puedeSobreescribirPrecio = in_array(auth()->user()->role, $rolesAutorizados)
            || auth()->user()->hasRole($rolesAutorizados);

        $productoIds = $data['producto_id'] ?? [];
        $cantidades  = $data['cantidad'] ?? [];
        $preciosCli  = $data['precio'] ?? [];
        $descuentoCli = $data['descuento'] ?? [];
        $tiposCli     = $data['descuento_tipo'] ?? [];

        $lineas = [];
        foreach ($productoIds as $i => $productoId) {
            if (!$productoId) continue;
            $producto = Producto::find($productoId);
            if (!$producto) {
                throw new \Exception('El producto #' . $productoId . ' no existe.');
            }
            $cantidad   = max(1, (int) ($cantidades[$i] ?? 1));
            $precioBD   = (float) $producto->precio;
            $precioCli  = (float) ($preciosCli[$i] ?? $precioBD);
            if (abs($precioCli - $precioBD) > 0.02 && !$puedeSobreescribirPrecio) {
                throw new \Exception("No autorizado para modificar el precio de \"{$producto->nombre}\".");
            }
            $precioBase = ($precioCli !== $precioBD && $puedeSobreescribirPrecio) ? $precioCli : $precioBD;
            $lineas[] = [
                'id'       => $productoId,
                'cantidad' => $cantidad,
                'precio'   => $precioBase,
                'subtotal' => round($precioBase * $cantidad, 2),
                'desc'     => (float) ($descuentoCli[$i] ?? 0),
                'tipo'     => $tiposCli[$i] ?? 'monto',
                'itbis_p'  => (float) ($producto->itbis_porcentaje ?? 0),
            ];
        }

        $subtotalTotal = (float) array_sum(array_column($lineas, 'subtotal'));
        $generalDescuento = max(0, (float) ($data['general_descuento'] ?? 0));

        $descuentosLinea = 0.0;
        foreach ($lineas as $linea) {
            if ($linea['desc'] <= 0) continue;
            $descuentosLinea += $linea['tipo'] === 'porcentaje'
                ? $linea['subtotal'] * min($linea['desc'], 100) / 100
                : $linea['desc'];
        }

        if ($subtotalTotal > 0) {
            $pctDescuento = (($descuentosLinea + $generalDescuento) / $subtotalTotal) * 100;
            if ($pctDescuento > 50 && !$puedeSobreescribirPrecio) {
                throw new \Exception('Descuentos superiores al 50% requieren autorización de administrador.');
            }
        }

        // Recalcular ITBIS sobre la base bruta autoritativa (descuento aplicado por línea)
        $itbisRecalculado = 0.0;
        foreach ($lineas as $line) {
            $descAplicado = $line['tipo'] === 'porcentaje'
                ? $line['subtotal'] * min($line['desc'], 100) / 100
                : $line['desc'];
            $baseFinal = max(0, $line['subtotal'] - $descAplicado);
            if ($generalDescuento > 0 && $subtotalTotal > 0) {
                $proporcion = $baseFinal / $subtotalTotal;
                $baseFinal = max(0, $baseFinal - ($generalDescuento * $proporcion));
            }
            $itbisRecalculado += $baseFinal * ($line['itbis_p'] / 100);
        }
        $itbisRecalculado = round($itbisRecalculado, 2);

        // Persistir arrays normalizados para procesarDetalles/procesarPago
        $data['producto_id']      = array_column($lineas, 'id');
        $data['cantidad']         = array_column($lineas, 'cantidad');
        $data['precio']           = array_column($lineas, 'precio');
        $data['subtotal']         = array_column($lineas, 'subtotal');
        $data['descuento']        = array_column($lineas, 'desc');
        $data['descuento_tipo']   = array_column($lineas, 'tipo');
        $data['itbis_porcentaje'] = array_column($lineas, 'itbis_p');
        $data['subtotal_final']   = $subtotalTotal;
        $descuentosLinea += $generalDescuento;
        $data['total'] = round($subtotalTotal - $descuentosLinea + $itbisRecalculado, 2);

        return DB::transaction(function () use ($data, $sesion, $metodo, $estado, $descuentosLinea, $generalDescuento, $itbisRecalculado) {
            $ncf = null;
            $ncfTipo = null;
            $ncfVencimiento = null;
            if (!empty($data['ncf_tipo'])) {
                $ncfTipo = $data['ncf_tipo'];
                $resultadoNcf = $this->ncfService->reservarNcfDentroTransaction($ncfTipo);
                $ncf = $resultadoNcf['ncf'];
                $ncfVencimiento = $resultadoNcf['fecha_vencimiento'];
            }

            $ventaExistente = null;
            if ($metodo === 'cuenta_abierta') {
                $ventaExistente = Venta::where('cliente_id', $data['cliente_id'])
                    ->where('estado', 'cuenta_abierta')
                    ->latest()
                    ->first();
            }

            if ($ventaExistente) {
                $venta = $ventaExistente;
                $venta->increment('subtotal', $data['subtotal_final'] ?? array_sum(array_map('floatval', $data['subtotal'])));
                $venta->increment('impuestos', $itbisRecalculado);
                $venta->increment('total', $data['total']);
                $venta->increment('descuento', $descuentosLinea);
                $venta->update(['fecha' => now()]);

                // NOTA: El balance del cliente se incrementa en procesarPago() para evitar doble incremento
            } else {
                $tipoComprobante = $data['tipo_comprobante'] ?? 'ncf';
                if ($tipoComprobante === 'ncf' && empty($data['ncf_tipo'])) {
                    $tipoComprobante = 'sin';
                }

                $venta = Venta::create([
                    'ncf'              => $ncf,
                    'ncf_tipo'         => $ncfTipo,
                    'ncf_vencimiento'  => $ncfVencimiento,
                    'tipo_comprobante' => $tipoComprobante,
                    'encf'             => null,
                    'user_id'          => Auth::id(),
                    'sucursal_id'      => session('sucursal_id'),
                    'caja_id'          => $sesion->caja_id,
                    'sesion_caja_id'   => $sesion->id,
                    'cliente_id'       => $data['cliente_id'],
                    'tipo_venta_id'    => $data['tipo_venta_id'],
                    'fecha'            => now(),
                    'impuestos'        => $itbisRecalculado,
                    'descuento'        => $descuentosLinea,
                    'general_descuento' => $generalDescuento,
                    'subtotal'         => $data['subtotal_final'] ?? array_sum(array_map('floatval', $data['subtotal'])),
                    'total'            => $data['total'],
                    'estado'           => $estado,
                    'propina'          => $data['propina'] ?? 0,
                    'delivery_fee'     => $data['delivery_fee'] ?? 0,
                    'cargo_servicio'   => $data['cargo_servicio'] ?? 0,
                    'tenant_id'        => Auth::user()->business_instance_id,
                ]);
            }

            $this->procesarDetalles($venta, $data, $ventaExistente);
            $this->procesarPago($venta, $sesion, $metodo, $estado, $data);

            // Calcular y guardar retenciones ITBIS vendedor
            $cliente = $venta->cliente;
            if ($cliente) {
                $retenciones = $this->retentionService->calcularRetencionesVenta($venta, $cliente);
                $this->retentionService->guardarRetencionesVenta($venta, $retenciones);
            }

            Event::dispatch(new \App\Events\SaleCreated($venta));

            return $venta;
        });

        // Punto único de emisión e-CF: fuera de la transacción, tras el commit (F0.4)
        if (($data['tipo_comprobante'] ?? 'ncf') === 'ecf' && empty($venta->encf)) {
            $this->procesarEcf($venta);
        }

        return $venta;
    }

    public function cancelSale(int $id, string $motivo): void
    {
        $motivo = strip_tags(trim($motivo));
        $tenantId = Auth::user()->business_instance_id;

        DB::transaction(function () use ($id, $motivo, $tenantId) {
            $venta = Venta::with(['detalles', 'ecfDocumento'])
                ->where('tenant_id', $tenantId)
                ->findOrFail($id);

            if ($venta->trashed()) {
                throw new \Exception('Esta venta ya fue anulada previamente.');
            }

            if ($venta->estado === 'anulada') {
                throw new \Exception('Esta venta ya se encuentra anulada.');
            }

            // Restaurar stock de productos
            $productIds = $venta->detalles->pluck('producto_id')->unique()->all();
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

            // Devolver deuda del cliente si estaba pendiente
            if ($venta->cliente_id && in_array($venta->estado, ['pendiente', 'cuenta_abierta'])) {
                $cliente = Cliente::where('id', $venta->cliente_id)
                    ->where('tenant_id', $tenantId)
                    ->first();
                if ($cliente) {
                    $montoDeuda = $venta->total - $venta->montoPagado();
                    if ($montoDeuda > 0) {
                        $cliente->decrement('balance_pendiente', $montoDeuda);
                    }
                }
            }

            // Devolver montos de caja
            if ($venta->sesion_caja_id) {
                $sesion = SesionCaja::where('id', $venta->sesion_caja_id)
                    ->where('tenant_id', $tenantId)
                    ->first();
                if ($sesion) {
                    foreach ($venta->pagos as $pago) {
                        match ($pago->metodo_pago) {
                            'efectivo'      => $sesion->decrement('ventas_efectivo', $pago->monto),
                            'tarjeta'       => $sesion->decrement('ventas_tarjeta', $pago->monto),
                            'transferencia' => $sesion->decrement('ventas_transferencia', $pago->monto),
                            default         => null,
                        };
                    }
                }
            }

            // Generar Nota de Crédito E34 si tiene e-CF aprobado
            if ($venta->ecfDocumento && $venta->ecfDocumento->estado === 'aprobado') {
                try {
                    $ecfService = app(EcfService::class);
                    $nc = $ecfService->generarNotaCredito($venta->ecfDocumento, 'Anulación de venta #' . $venta->id . ': ' . $motivo);
                    Log::info('Nota de crédito E34 generada por anulación', [
                        'venta_id' => $venta->id,
                        'nc_encf' => $nc->encf,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('Falló generación de E34 por anulación', [
                        'venta_id' => $venta->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Actualizar estado y soft delete
            $venta->update(['estado' => 'anulada']);
            $venta->delete();

            Log::info('Venta anulada (soft delete)', [
                'venta_id' => $venta->id, 'total' => $venta->total,
                'motivo' => $motivo, 'user_id' => Auth::id(),
            ]);

            Event::dispatch(new \App\Events\SaleCancelled($venta, $motivo));
        });
    }

    public function getCreationData(): array
    {
        $tenantId = Auth::user()->business_instance_id;
        
        $isElevated = in_array(Auth::user()->role, ['admin', 'owner', 'admin-business', 'root'])
            || Auth::user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        $sesionesActivas = SesionCaja::with('caja')
            ->where('estado', 'abierta');

        if (!$isElevated) {
            $sesionesActivas->where('user_id', Auth::id());
        }

        $sesiones = $sesionesActivas->latest('fecha_apertura')->get();

        if ($sesiones->isEmpty()) {
            return ['sesiones' => collect(), 'sesion' => null];
        }

        // Default a la sesión solicitada (?sesion_id=) o a la más reciente
        $sesionIdSolicitada = (int) request()->query('sesion_id', 0);
        $sesion = $sesiones->firstWhere('id', $sesionIdSolicitada) ?? $sesiones->first();

        $clienteConsumidorFinal = Cliente::consumidorFinal($tenantId);

        $clientes   = Cliente::where('tenant_id', $tenantId)->orderBy('nombre')->get();
        $tiposVenta = \App\Models\TipoVenta::orderBy('nombre')->get();
        $tipoVentaDefault = $tiposVenta->firstWhere('nombre', 'Contado') ?? $tiposVenta->first();
        $almacenes = \App\Models\Almacen::where('tenant_id', $tenantId)->orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $almacenes = $almacenes->where('sucursal_id', $sucursalId);
        }
        $almacenes = $almacenes->get();

        if ($almacenes->isEmpty()) {
            $defaultAlmacen = \App\Models\Almacen::where('tenant_id', $tenantId)->first();
            if ($defaultAlmacen) {
                $almacenes = collect([$defaultAlmacen]);
            }
        }

        $productos = Producto::where('tenant_id', $tenantId)
            ->orderBy('nombre')
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen', 'categoria_id')
            ->get()
            ->map(fn($p) => $p->setAttribute('imagen_url', $p->imagen_url));

        // Apply restaurante_valida_stock setting (shared with restaurant module)
        $validaStock = $this->validaStock();
        if ($validaStock) {
            $productos = $productos->filter(fn($p) => $p->stock > 0)->values();
        }

        $stockPorProductoAlmacen = AlmacenMovimiento::query()
            ->where('tenant_id', $tenantId)
            ->selectRaw('producto_id, almacen_id, SUM(CASE WHEN tipo = "entrada" THEN cantidad ELSE -cantidad END) as stock')
            ->groupBy('producto_id', 'almacen_id')
            ->get();
        $stocks = [];
        foreach ($stockPorProductoAlmacen as $row) {
            $stocks[$row->producto_id][$row->almacen_id] = (int) $row->stock;
        }
        foreach ($productos as $producto) {
            $stocks[$producto->id] ??= [];
        }

        $ncfSequences = \App\Models\NcfSequence::where('tenant_id', $tenantId)
            ->where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->get();

        $cajas = \App\Models\Caja::where('tenant_id', $tenantId)->activas()->orderBy('nombre')->get();

        $productosJs = $productos->map(fn($p) => [
            'id'           => (int) $p->id,
            'nombre'       => $p->nombre,
            'codigo_barras'=> $p->codigo_barras,
            'precio'       => (float) $p->precio,
            'precio_compra'=> (float) ($p->precio_compra ?? 0),
            'itbis_p'      => (float) ($p->itbis_porcentaje ?? 18),
            'stock'        => (int) $p->stock,
            'ventas_count' => (int) ($p->ventas_count ?? 0),
            'unidad_medida'=> $p->unidad_medida ?? 'Unidad',
            'imagen_url'   => $p->imagen_url,
            'categoria_id' => (int) ($p->categoria_id ?? 0),
        ])->values()->all();

        $categoriasJs = Categoria::where('tenant_id', $tenantId)->orderBy('nombre')->get(['id', 'nombre'])->toArray();

        $puedeModificarPrecio = in_array(Auth::user()->role, ['admin', 'admin-business', 'root', 'gerente'])
            || Auth::user()->hasAnyRole(['admin', 'admin-business', 'root', 'gerente']);

        $clientesJs = $clientes->map(fn($c) => [
            'id'         => (int) $c->id,
            'nombre'     => $c->nombre,
            'tipo'       => $c->tipo_cliente ?? 'consumo',
            'deuda'      => (float) ($c->balance_pendiente ?? 0),
            'limite'     => (float) ($c->limite_credito ?? 0),
            'es_final'   => $c->id === $clienteConsumidorFinal->id,
            'rnc'        => $c->rnc ?? $c->rnc_cedula ?? '',
            'rnc_cedula' => $c->rnc_cedula ?? $c->rnc ?? '',
            'tipo_cliente' => $c->tipo_cliente ?? 'consumo',
        ])->values()->all();

        return compact(
            'clientes', 'tiposVenta', 'productos', 'almacenes', 'stocks', 'ncfSequences',
            'sesiones', 'sesion', 'cajas', 'clienteConsumidorFinal', 'tipoVentaDefault',
            'productosJs', 'clientesJs', 'categoriasJs', 'validaStock', 'puedeModificarPrecio'
        );
    }

    private function validaStock(): bool
    {
        $user = Auth::user();
        if (!$user?->businessInstance) return true;
        return ($user->businessInstance->configuracion['restaurante_valida_stock'] ?? '1') === '1';
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
                    Log::warning('No se pudo reenviar e-CF de la venta #' . $venta->id . ': ' . $e->getMessage());
                }
            }
            return;
        }

        if ($venta->cliente_id) {
            $cliente = $venta->cliente;
            if ($cliente && !empty($cliente->rnc_cedula)) {
                $tipoDoc = $cliente->tipo_documento ?? RncValidator::inferirTipo($cliente->rnc_cedula);
                if (!RncValidator::validar($cliente->rnc_cedula, $tipoDoc)) {
                    throw new \Exception("El RNC/Cédula del cliente ({$cliente->rnc_cedula}) no es válido según DGII.");
                }
            } elseif ($cliente && in_array($venta->tipo_ecf ?? '', ['E31', 'E44', 'E45'])) {
                throw new \Exception("Los e-CF tipo Crédito Fiscal requieren un cliente con RNC válido.");
            }
        }
        try {
            $ecf = $this->ecfService->generarEcf($venta);
            $ecfFirmado = $this->ecfService->firmar($ecf);
            $this->ecfService->enviar($ecfFirmado);
        } catch (\Throwable $e) {
            Log::warning('No se pudo generar e-CF para la venta #' . $venta->id . ': ' . $e->getMessage());
        }
    }

    private function procesarDetalles(Venta $venta, array $data, ?Venta $ventaExistente): void
    {
        $tenantId = Auth::user()->business_instance_id;

        // Ensure we always have a fallback almacen for the FK constraint
        $fallbackAlmacen = \App\Models\Almacen::where('tenant_id', $tenantId)->first();

        $productoIds = $data['producto_id'] ?? [];
        $cantidades  = $data['cantidad'] ?? [];
        $precios     = $data['precio'] ?? [];
        $subtotales  = $data['subtotal'] ?? [];
        $almacenes   = $data['almacen_id'] ?? [];
        
        $maxItems = count($productoIds);
        for ($i = 0; $i < $maxItems; $i++) {
            $productoId = $productoIds[$i] ?? null;
            if (!$productoId) continue;
            
            $cantidad = $cantidades[$i] ?? 0;
            $precio = $precios[$i] ?? 0;
            $subtotal = $subtotales[$i] ?? 0;
            $almacenId = isset($almacenes[$i]) && (int)$almacenes[$i] > 0
                ? (int)$almacenes[$i]
                : ($fallbackAlmacen?->id);
            $descuento = (float) ($data['descuento'][$i] ?? 0);
            $descuentoTipo = $data['descuento_tipo'][$i] ?? 'monto';
            $itbisPorcentaje = (float) ($data['itbis_porcentaje'][$i] ?? 0);

            $producto = Producto::findOrFail($productoId);

            if ($this->validaStock()) {
                $disponiblePorAlmacen = $almacenId ? $this->checkStock($productoId, $almacenId) : $producto->stock;
                if ($disponiblePorAlmacen < $cantidad || $producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre} (Disponible en almacén: {$disponiblePorAlmacen}, Stock global: {$producto->stock})");
                }
            }

            VentaDetalle::create([
                'venta_id'         => $venta->id,
                'producto_id'      => $productoId,
                'cantidad'         => $cantidad,
                'precio_unitario'  => $precio,
                'subtotal'         => $subtotal,
                'descuento'        => $descuento,
                'descuento_tipo'   => $descuentoTipo,
                'itbis_porcentaje' => $itbisPorcentaje,
                'almacen_id'       => $almacenId,
                'tenant_id'        => $tenantId,
            ]);

            if ($this->validaStock()) {
                AlmacenMovimiento::create([
                    'tenant_id'   => $tenantId,
                    'producto_id' => $productoId,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'nota'        => 'Venta #' . $venta->id . ($ventaExistente ? ' (Adición)' : ''),
                    'user_id'     => Auth::id(),
                ]);

                $producto->decrement('stock', $cantidad);

                if ($producto->stock <= ($producto->stock_minimo ?? 5)) {
                    Event::dispatch(new \App\Events\StockCritical($producto, $producto->stock));
                }
            }

            $producto->increment('ventas_count', $cantidad);
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
                throw new \Exception("La suma de los montos mixtos (RD$ " . number_format($mixtoSum, 2) . ") debe ser igual al total (RD$ " . number_format($data['total'], 2) . ").");
            }
            $mixtos = [
                'efectivo'      => $mixtoEfectivo,
                'tarjeta'       => $mixtoTarjeta,
                'transferencia' => $mixtoTransferencia,
            ];
            foreach ($mixtos as $tipo => $monto) {
                if ($monto > 0) {
                    Pago::create([
                        'tenant_id'      => Auth::user()->business_instance_id,
                        'venta_id'       => $venta->id,
                        'caja_id'        => $sesion->caja_id,
                        'sesion_caja_id' => $sesion->id,
                        'monto'          => $monto,
                        'metodo_pago'    => $tipo,
                        'nota'           => 'Pago mixto (' . ucfirst($tipo) . ')',
                        'fecha_pago'     => now(),
                    ]);
                    match ($tipo) {
                        'efectivo'      => $sesion->increment('ventas_efectivo', $monto),
                        'tarjeta'       => $sesion->increment('ventas_tarjeta', $monto),
                        'transferencia' => $sesion->increment('ventas_transferencia', $monto),
                        default         => null,
                    };
                }
            }
            return;
        }

        $pago = Pago::create([
            'tenant_id'      => Auth::user()->business_instance_id,
            'venta_id'       => $venta->id,
            'caja_id'        => $sesion->caja_id,
            'sesion_caja_id' => $sesion->id,
            'monto'          => $data['total'],
            'metodo_pago'    => $metodo,
            'nota'           => 'Pago automático (Venta ' . ucfirst($metodo) . ')',
            'fecha_pago'     => now(),
        ]);

        match ($metodo) {
            'efectivo'      => $sesion->increment('ventas_efectivo', $data['total']),
            'tarjeta'       => $sesion->increment('ventas_tarjeta', $data['total']),
            'transferencia' => $sesion->increment('ventas_transferencia', $data['total']),
            default         => null,
        };

        Event::dispatch(new \App\Events\PaymentReceived($pago));
    }
}
