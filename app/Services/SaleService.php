<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Support\RncValidator;
use App\Services\Ecf\EcfService;
use App\Services\NcfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleService
{
    protected NcfService $ncfService;
    protected EcfService $ecfService;

    public function __construct(NcfService $ncfService, EcfService $ecfService)
    {
        $this->ncfService = $ncfService;
        $this->ecfService = $ecfService;
    }

    public function createSale(array $data, SesionCaja $sesion): Venta
    {
        if (empty($data['cliente_id'])) {
            $consumidorFinal = Cliente::firstOrCreate(
                ['nombre' => 'Consumidor Final'],
                ['limite_credito' => 0, 'balance_pendiente' => 0, 'tipo_cliente' => 'consumo']
            );
            $data['cliente_id'] = $consumidorFinal->id;
        }

        $metodo = $data['metodo_pago'] ?? 'efectivo';
        $estado = match ($metodo) {
            'fiado' => 'pendiente',
            'cuenta_abierta' => 'cuenta_abierta',
            default => 'completada',
        };

        $subtotalTotal = array_sum($data['subtotal'] ?? []);
        if ($subtotalTotal > 0) {
            $descuentosLinea = 0;
            foreach (($data['descuento'] ?? []) as $i => $desc) {
                $desc = (float) ($desc ?? 0);
                if ($desc <= 0) continue;
                $tipo = $data['descuento_tipo'][$i] ?? 'monto';
                $lineSub = (float) ($data['subtotal'][$i] ?? 0);
                if ($tipo === 'porcentaje') {
                    $descuentosLinea += $lineSub * min($desc, 100) / 100;
                } else {
                    $descuentosLinea += $desc;
                }
            }
            $pctDescuento = ($descuentosLinea / $subtotalTotal) * 100;
            if ($pctDescuento > 50 && !auth()->user()->hasRole('admin')) {
                throw new \Exception('Descuentos superiores al 50% requieren autorización de administrador.');
            }
        }

        return DB::transaction(function () use ($data, $sesion, $metodo, $estado) {
            $ncf = null;
            if (!empty($data['ncf_tipo'])) {
                $ncf = $this->ncfService->getNextNcf($data['ncf_tipo']);
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
                $venta->increment('subtotal', $data['subtotal_final'] ?? array_sum($data['subtotal']));
                $venta->increment('impuestos', $data['impuestos'] ?? 0);
                $venta->increment('total', $data['total']);
                $venta->update(['fecha' => now()]);
            } else {
                $tipoComprobante = $data['tipo_comprobante'] ?? 'ncf';
                if ($tipoComprobante === 'ncf' && empty($data['ncf_tipo'])) {
                    $tipoComprobante = 'sin';
                }

                $venta = Venta::create([
                    'ncf'              => $ncf,
                    'ncf_tipo'         => $data['ncf_tipo'] ?? null,
                    'ncf_vencimiento'  => null,
                    'tipo_comprobante' => $tipoComprobante,
                    'encf'             => null,
                    'user_id'          => Auth::id(),
                    'sucursal_id'      => session('sucursal_id'),
                    'caja_id'          => $sesion->caja_id,
                    'sesion_caja_id'   => $sesion->id,
                    'cliente_id'       => $data['cliente_id'],
                    'tipo_venta_id'    => $data['tipo_venta_id'],
                    'fecha'            => now(),
                    'impuestos'        => $data['impuestos'] ?? 0,
                    'descuento'        => is_array($data['descuento'] ?? null) ? ($data['descuento'][0] ?? 0) : ($data['descuento'] ?? 0),
                    'subtotal'         => $data['subtotal_final'] ?? array_sum($data['subtotal']),
                    'total'            => $data['total'],
                    'estado'           => $estado,
                    'propina'          => $data['propina'] ?? 0,
                    'tenant_id'        => Auth::user()->business_instance_id,
                ]);

                if ($tipoComprobante === 'ecf') {
                    $this->procesarEcf($venta);
                }
            }

            $this->procesarDetalles($venta, $data, $ventaExistente);
            $this->procesarPago($venta, $sesion, $metodo, $estado, $data);

            return $venta;
        });
    }

    public function cancelSale(int $id, string $motivo): void
    {
        $motivo = strip_tags(trim($motivo));

        DB::transaction(function () use ($id, $motivo) {
            $venta = Venta::with('detalles')->findOrFail($id);

            foreach ($venta->detalles as $detalle) {
                $almacenId = ($detalle->almacen_id > 0) ? $detalle->almacen_id : 1;
                AlmacenMovimiento::create([
                    'tenant_id'   => Auth::user()->business_instance_id,
                    'producto_id' => $detalle->producto_id,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'entrada',
                    'cantidad'    => $detalle->cantidad,
                    'nota'        => 'ANULACIÓN Venta #' . $venta->id . ' | Motivo: ' . $motivo,
                    'user_id'     => Auth::id(),
                ]);

                $productoObj = Producto::find($detalle->producto_id);
                if ($productoObj) {
                    $productoObj->increment('stock', $detalle->cantidad);
                }
            }

            if ($venta->cliente_id && in_array($venta->estado, ['pendiente', 'cuenta_abierta'])) {
                $cliente = Cliente::find($venta->cliente_id);
                if ($cliente) {
                    $montoDeuda = $venta->total - $venta->montoPagado();
                    if ($montoDeuda > 0) {
                        $cliente->decrement('balance_pendiente', $montoDeuda);
                    }
                }
            }

            if ($venta->sesion_caja_id) {
                $sesion = SesionCaja::find($venta->sesion_caja_id);
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

            Log::info('Venta anulada', [
                'venta_id' => $venta->id, 'total' => $venta->total,
                'motivo' => $motivo, 'user_id' => Auth::id(),
            ]);

            $venta->detalles()->delete();
            $venta->pagos()->delete();
            $venta->delete();
        });
    }

    public function getCreationData(): array
    {
        $sesion = SesionCaja::with('caja')
            ->where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if (!$sesion) {
            return ['sesion' => null];
        }

        $clienteConsumidorFinal = Cliente::firstOrCreate(
            ['nombre' => 'Consumidor Final'],
            ['limite_credito' => 0, 'balance_pendiente' => 0, 'tipo_cliente' => 'consumo']
        );

        $clientes   = Cliente::orderBy('nombre')->get();
        $tiposVenta = \App\Models\TipoVenta::orderBy('nombre')->get();
        $tipoVentaDefault = $tiposVenta->firstWhere('nombre', 'Contado') ?? $tiposVenta->first();
        $almacenes = \App\Models\Almacen::orderBy('nombre');
        if ($sucursalId = session('sucursal_id')) {
            $almacenes = $almacenes->where('sucursal_id', $sucursalId);
        }
        $almacenes = $almacenes->get();
        // Fallback: if no almacenes in this sucursal, get any almacen in the tenant
        if ($almacenes->isEmpty()) {
            $almacenes = \App\Models\Almacen::orderBy('nombre')->limit(1)->get();
        }

        $productos = Producto::orderBy('nombre')
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen', 'categoria_id')
            ->get()
            ->map(fn($p) => $p->setAttribute('imagen_url', $p->imagen_url));

        // Apply restaurante_valida_stock setting (shared with restaurant module)
        $validaStock = true;
        $user = Auth::user();
        if ($user && $user->businessInstance) {
            $config = $user->businessInstance->configuracion ?? [];
            $validaStock = ($config['restaurante_valida_stock'] ?? '1') === '1';
        }
        if ($validaStock) {
            $productos = $productos->filter(fn($p) => $p->stock > 0)->values();
        }

        $stockPorProductoAlmacen = AlmacenMovimiento::query()
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

        $ncfSequences = \App\Models\NcfSequence::where('activo', true)
            ->where('fecha_vencimiento', '>=', now())
            ->get();

        $cajas = \App\Models\Caja::activas()->orderBy('nombre')->get();

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

        $categoriasJs = Categoria::orderBy('nombre')->get(['id', 'nombre'])->toArray();

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
            'sesion', 'cajas', 'clienteConsumidorFinal', 'tipoVentaDefault',
            'productosJs', 'clientesJs', 'categoriasJs', 'validaStock'
        );
    }

    public function checkStock(int $productoId, int $almacenId): int
    {
        $entrada = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)->where('tipo', 'entrada')->sum('cantidad');
        $salida = AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)->where('tipo', 'salida')->sum('cantidad');
        return $entrada - $salida;
    }

    private function procesarEcf(Venta $venta): void
    {
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
        $validaStock = true;
        $user = Auth::user();
        if ($user && $user->businessInstance) {
            $config = $user->businessInstance->configuracion ?? [];
            $validaStock = ($config['restaurante_valida_stock'] ?? '1') === '1';
        }

        // Ensure we always have a fallback almacen for the FK constraint
        $fallbackAlmacen = \App\Models\Almacen::first();
        if (!$fallbackAlmacen) {
            $fallbackAlmacen = \App\Models\Almacen::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'nombre'      => 'General',
                'ubicacion'   => 'Principal',
            ]);
        }

        foreach ($data['producto_id'] as $key => $productoId) {
            $almacenId = isset($data['almacen_id'][$key]) ? (int)$data['almacen_id'][$key] : $fallbackAlmacen->id;
            $cantidad = $data['cantidad'][$key];

            $producto = Producto::findOrFail($productoId);

            if ($validaStock) {
                $disponiblePorAlmacen = $this->checkStock($productoId, $almacenId);
                if ($disponiblePorAlmacen < $cantidad || $producto->stock < $cantidad) {
                    throw new \Exception("Stock insuficiente para: {$producto->nombre} (Disponible en almacén: {$disponiblePorAlmacen}, Stock global: {$producto->stock})");
                }
            }

            VentaDetalle::create([
                'venta_id'        => $venta->id,
                'producto_id'     => $productoId,
                'cantidad'        => $cantidad,
                'precio_unitario' => $data['precio'][$key],
                'subtotal'        => $data['subtotal'][$key],
                'almacen_id'      => $almacenId,
                'tenant_id'       => Auth::user()->business_instance_id,
            ]);

            if ($validaStock) {
                AlmacenMovimiento::create([
                    'tenant_id'   => Auth::user()->business_instance_id,
                    'producto_id' => $productoId,
                    'almacen_id'  => $almacenId,
                    'tipo'        => 'salida',
                    'cantidad'    => $cantidad,
                    'nota'        => 'Venta #' . $venta->id . ($ventaExistente ? ' (Adición)' : ''),
                    'user_id'     => Auth::id(),
                ]);

                $producto->decrement('stock', $cantidad);
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
            $mixtos = [
                'efectivo'      => (float) ($data['mixto_efectivo'] ?? 0),
                'tarjeta'       => (float) ($data['mixto_tarjeta'] ?? 0),
                'transferencia' => (float) ($data['mixto_transferencia'] ?? 0),
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

        Pago::create([
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
    }
}
