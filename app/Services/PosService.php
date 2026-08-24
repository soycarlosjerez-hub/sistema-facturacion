<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\LavaderoPaquete;
use App\Models\LavaderoServicio;
use App\Models\Producto;
use App\Models\SesionCaja;
use App\Models\SystemSetting;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Pago;
use App\Traits\TenantScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function checkout(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $tenantId = Auth::user()->business_instance_id;
            $sesionActiva = $this->getSesionCajaActiva();

            // Validate client
            if (empty($data['cliente_id'])) {
                $consumidorFinal = Cliente::whereNull('tenant_id')
                    ->orWhere('tenant_id', $tenantId)
                    ->where('nombre', 'Consumidor Final')
                    ->first();

                if (!$consumidorFinal) {
                    $consumidorFinal = Cliente::create([
                        'nombre'   => 'Consumidor Final',
                        'rnc_cedula' => '00000000000',
                        'tenant_id' => $tenantId,
                    ]);
                }

                $data['cliente_id'] = $consumidorFinal->id;
            }

            $cliente = Cliente::find($data['cliente_id']);

            // Build line items from services, productos, paquetes
            $lineItems = [];

            // Servicios de lavadero
            if (!empty($data['servicios'])) {
                foreach ($data['servicios'] as $s) {
                    $servicio = LavaderoServicio::find($s['id'] ?? $s);
                    if (!$servicio) continue;

                    $lineItems[] = [
                        'tipo'         => 'servicio',
                        'nombre'       => $servicio->nombre,
                        'precio'       => (float) ($s['precio'] ?? $servicio->precio),
                        'cantidad'     => 1,
                        'subtotal'     => (float) ($s['precio'] ?? $servicio->precio),
                        'itbis_p'      => (float) ($cliente?->itbis_exento ? 0 : SystemSetting::itbisDefault()),
                    ];
                }
            }

            // Productos físicos
            if (!empty($data['productos'])) {
                foreach ($data['productos'] as $p) {
                    $producto = Producto::find($p['id'] ?? $p);
                    if (!$producto) continue;

                    $cantidad = $p['cantidad'] ?? 1;
                    $precio = (float) ($p['precio'] ?? $producto->precio);
                    $subtotal = round($precio * $cantidad, 2);
                    $itbisPct = $cliente && $cliente->itbis_exento ? 0 : ($producto->itbis_porcentaje ?? SystemSetting::itbisDefault());

                    $lineItems[] = [
                        'tipo'         => 'producto',
                        'nombre'       => $producto->nombre,
                        'precio'       => $precio,
                        'cantidad'     => $cantidad,
                        'subtotal'     => $subtotal,
                        'itbis_p'      => $itbisPct,
                        'producto_id'  => $producto->id,
                        'stock'        => $producto->stock,
                    ];
                }
            }

            // Paquetes de lavadero
            if (!empty($data['paquetes'])) {
                foreach ($data['paquetes'] as $pk) {
                    $paquete = LavaderoPaquete::find($pk['id'] ?? $pk);
                    if (!$paquete) continue;

                    $cantidad = $pk['cantidad'] ?? 1;
                    $precio = (float) ($paquete->precio);
                    $subtotal = round($precio * $cantidad, 2);

                    $lineItems[] = [
                        'tipo'         => 'paquete',
                        'nombre'       => $paquete->nombre . ' (x' . $cantidad . ')',
                        'precio'       => $precio,
                        'cantidad'     => $cantidad,
                        'subtotal'     => $subtotal,
                        'itbis_p'      => (float) ($cliente?->itbis_exento ? 0 : SystemSetting::itbisDefault()),
                        'paquete_id'   => $paquete->id,
                    ];
                }
            }

            if (empty($lineItems)) {
                throw new \Exception('No hay items para vender.');
            }

            // Calcular totales
            $subtotal = array_sum(array_column($lineItems, 'subtotal'));
            $itbisTotal = array_reduce($lineItems, function ($carry, $item) {
                return $carry + ($item['subtotal'] * ($item['itbis_p'] / 100));
            }, 0.0);
            $itbisTotal = round($itbisTotal, 2);
            $total = round($subtotal + $itbisTotal, 2);

            // Crear venta
            $metodoPago = $data['metodo_pago'] ?? 'efectivo';
            $estado = $metodoPago === 'fiado' ? 'pendiente' : 'completada';

            $venta = Venta::create([
                'user_id'          => Auth::id(),
                'sucursal_id'      => session('sucursal_id'),
                'caja_id'          => $sesionActiva?->caja_id,
                'sesion_caja_id'   => $sesionActiva?->id,
                'cliente_id'       => $data['cliente_id'],
                'tipo_venta_id'    => $data['tipo_venta_id'] ?? null,
                'fecha'            => now(),
                'subtotal'         => $subtotal,
                'impuestos'        => $itbisTotal,
                'total'            => $total,
                'estado'           => $estado,
                'notas'            => 'Venta mixta (lavadero + tienda)',
                'tenant_id'        => $tenantId,
            ]);

            // Crear detalles de venta
            foreach ($lineItems as $item) {
                VentaDetalle::create([
                    'venta_id'         => $venta->id,
                    'producto_id'      => $item['producto_id'] ?? null,
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio'],
                    'subtotal'         => $item['subtotal'],
                    'notas'            => $item['nombre'],
                    'itbis_porcentaje' => $item['itbis_p'],
                    'tenant_id'        => $tenantId,
                ]);

                // Descuento de stock si es producto físico
                if ($item['tipo'] === 'producto' && isset($item['producto_id']) && $item['stock'] >= $item['cantidad']) {
                    Producto::where('id', $item['producto_id'])->decrement('stock', $item['cantidad']);
                }
            }

            // Registrar pago
            if (in_array($metodoPago, ['efectivo', 'tarjeta', 'transferencia'])) {
                Pago::create([
                    'tenant_id'      => $tenantId,
                    'venta_id'       => $venta->id,
                    'caja_id'        => $sesionActiva?->caja_id,
                    'sesion_caja_id' => $sesionActiva?->id,
                    'monto'          => $total,
                    'metodo_pago'    => $metodoPago,
                    'fecha_pago'     => now(),
                ]);

                if ($sesionActiva) {
                    match ($metodoPago) {
                        'efectivo'      => $sesionActiva->increment('ventas_efectivo', $total),
                        'tarjeta'       => $sesionActiva->increment('ventas_tarjeta', $total),
                        'transferencia' => $sesionActiva->increment('ventas_transferencia', $total),
                    };
                }
            }

            return [
                'success' => true,
                'venta'   => $venta->load(['cliente', 'detalles', 'pagos']),
                'total'   => $total,
                'venta_id'=> $venta->id,
            ];
        });
    }

    public function quickSearch(string $query, ?string $linea = null): \Illuminate\Support\Collection
    {
        $queryBuilder = Producto::activos()
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('codigo_barras', 'like', "%{$query}%");
            })
            ->select('id', 'nombre', 'codigo_barras', 'precio', 'stock', 'categoria_id', 'imagen')
            ->orderBy('stock', 'desc');

        if ($linea) {
            $queryBuilder->where('categoria_id', fn ($q) => \App\Models\Categoria::where('slug', $linea)->value('id') ?: 0);
        }

        return $queryBuilder->limit(20)->get();
    }

    public function holdSale(array $data): array
    {
        $sessionId = 'hold_' . Auth::id() . '_' . now()->timestamp;

        session([
            'hold_' . $sessionId => [
                'cliente_id' => $data['cliente_id'] ?? null,
                'vehiculo_id'=> $data['vehiculo_id'] ?? null,
                'servicios'  => $data['servicios'] ?? [],
                'productos'  => $data['productos'] ?? [],
                'paquetes'   => $data['paquetes'] ?? [],
                'metodo_pago'=> $data['metodo_pago'] ?? 'efectivo',
                'total'      => $data['total'] ?? 0,
                'created_at' => now()->toIso8601String(),
            ],
        ]);

        return [
            'success'   => true,
            'hold_id'   => $sessionId,
            'total'     => $data['total'] ?? 0,
            'items_count' => count($data['servicios'] ?? []) + count($data['productos'] ?? []) + count($data['paquetes'] ?? []),
        ];
    }

    public function restoreSale(string $holdId): ?array
    {
        $key = 'hold_' . Auth::id() . '_' . $holdId;
        // Try different patterns
        $holdData = session()->get('hold_' . $holdId);

        if (!$holdData) {
            // Try the full pattern
            $holdData = session()->get('hold_' . $holdId);
        }

        if (!$holdData) {
            throw new \Exception('Venta en espera no encontrada.');
        }

        return $holdData;
    }

    public function calculateTotal(array $items): array
    {
        $subtotal = 0;
        $itbisTotal = 0;
        $itbisByRate = [];

        foreach ($items as $item) {
            $itemSubtotal = ($item['precio'] ?? 0) * ($item['cantidad'] ?? 1);
            $itbisPct = $item['itbis_p'] ?? SystemSetting::itbisDefault();

            $subtotal += $itemSubtotal;
            $itbisAmount = round($itemSubtotal * ($itbisPct / 100), 2);
            $itbisTotal += $itbisAmount;

            if (!isset($itbisByRate[$itbisPct])) {
                $itbisByRate[$itbisPct] = 0;
            }
            $itbisByRate[$itbisPct] += $itbisAmount;
        }

        return [
            'subtotal'      => round($subtotal, 2),
            'itbis_total'   => round($itbisTotal, 2),
            'itbis_by_rate' => $itbisByRate,
            'total'         => round($subtotal + $itbisTotal, 2),
        ];
    }

    protected function getSesionCajaActiva(): ?SesionCaja
    {
        $isElevated = in_array(Auth::user()->role, ['admin', 'owner', 'admin-business', 'root'])
            || Auth::user()->hasAnyRole(['admin', 'owner', 'admin-business', 'root']);

        $query = SesionCaja::where('estado', 'abierta');
        if (!$isElevated) {
            $query->where('user_id', Auth::id());
        }

        return $query->first();
    }
}
