<?php

namespace App\Services;

use App\Models\AlmacenMovimiento;
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
        $metodo = $data['metodo_pago'] ?? 'efectivo';
        $estado = match ($metodo) {
            'fiado' => 'pendiente',
            'cuenta_abierta' => 'cuenta_abierta',
            default => 'completada',
        };

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
        $almacenes  = \App\Models\Almacen::orderBy('nombre')->get();

        $productos = Producto::orderBy('nombre')
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'precio_compra', 'itbis_porcentaje', 'stock', 'ventas_count', 'unidad_medida', 'imagen')
            ->get()
            ->map(fn($p) => $p->setAttribute('imagen_url', $p->imagen_url));

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
        ])->values()->all();

        $clientesJs = $clientes->map(fn($c) => [
            'id'       => (int) $c->id,
            'nombre'   => $c->nombre,
            'tipo'     => $c->tipo_cliente ?? 'consumo',
            'deuda'    => (float) ($c->balance_pendiente ?? 0),
            'es_final' => $c->id === $clienteConsumidorFinal->id,
        ])->values()->all();

        return compact(
            'clientes', 'tiposVenta', 'productos', 'almacenes', 'stocks', 'ncfSequences',
            'sesion', 'cajas', 'clienteConsumidorFinal', 'tipoVentaDefault',
            'productosJs', 'clientesJs'
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
        foreach ($data['producto_id'] as $key => $productoId) {
            $almacenId = $data['almacen_id'][$key] ?? 1;
            $cantidad = $data['cantidad'][$key];

            $disponible = $this->checkStock($productoId, $almacenId);
            if ($disponible < $cantidad) {
                $p = Producto::find($productoId);
                throw new \Exception("Stock insuficiente para: {$p->nombre} (Disponible: {$disponible})");
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

            AlmacenMovimiento::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'producto_id' => $productoId,
                'almacen_id'  => $almacenId,
                'tipo'        => 'salida',
                'cantidad'    => $cantidad,
                'nota'        => 'Venta #' . $venta->id . ($ventaExistente ? ' (Adición)' : ''),
                'user_id'     => Auth::id(),
            ]);

            $producto = Producto::find($productoId);
            $producto->decrement('stock', $cantidad);
            $producto->increment('ventas_count', $cantidad);
        }
    }

    private function procesarPago(Venta $venta, SesionCaja $sesion, string $metodo, string $estado, array $data): void
    {
        if (in_array($estado, ['pendiente', 'cuenta_abierta'])) {
            $cliente = Cliente::find($data['cliente_id']);
            $cliente?->increment('balance_pendiente', $data['total']);
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
