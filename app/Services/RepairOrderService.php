<?php

namespace App\Services;

use App\Models\OrdenReparacion;
use App\Models\DetallePiezaReparacion;
use App\Models\Equipo;
use App\Models\AlmacenMovimiento;
use App\Models\Producto;
use App\Models\Garantia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RepairOrderService
{
    public function __construct() {}

    public function crearOrden(array $data, int $userId): OrdenReparacion
    {
        $data['user_id'] = $userId;
        $data['numero_orden'] = OrdenReparacion::generarNumeroOrden();
        $data['estado'] = 'recibido';
        $data['fecha_recibo'] = $data['fecha_recibo'] ?? now();

        if (isset($data['fecha_entrega_estimada'])) {
            $data['fecha_entrega_estimada'] = Carbon::parse($data['fecha_entrega_estimada']);
        }

        return DB::transaction(function () use ($data, $userId) {
            $orden = OrdenReparacion::create($data);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'en_reparacion']);
            }

            if ($data['garantia_extendida'] ?? false) {
                Garantia::create([
                    'orden_reparacion_id' => $orden->id,
                    'equipo_id' => $orden->equipo_id,
                    'tipo' => 'servicio',
                    'fecha_inicio' => now(),
                    'fecha_fin' => now()->addDays(90),
                    'cobertura' => 0,
                    'estado' => 'activa',
                ]);
            }

            return $orden;
        });
    }

    public function actualizarOrden(int $orderId, array $data): OrdenReparacion
    {
        $orden = OrdenReparacion::findOrFail($orderId);

        $data['user_id'] = auth()->id();

        if (isset($data['fecha_entrega_estimada'])) {
            $data['fecha_entrega_estimada'] = isset($data['fecha_entrega_estimada']) ? Carbon::parse($data['fecha_entrega_estimada']) : null;
        }

        if (isset($data['costo_piezas']) || isset($data['mano_obra']) || isset($data['descuento'])) {
            $orden->fill($data);
            $orden->calcularTotales();
        } else {
            $orden->update($data);
        }

        return $orden;
    }

    public function agregarPieza(int $orderId, int $productoId, int $cantidad): OrdenReparacion
    {
        $producto = Producto::findOrFail($productoId);

        return DB::transaction(function () use ($orderId, $producto, $cantidad) {
            $orden = OrdenReparacion::findOrFail($orderId);

            DetallePiezaReparacion::create([
                'orden_reparacion_id' => $orden->id,
                'producto_id' => $producto->id,
                'cantidad' => $cantidad,
                'costo_unitario' => $producto->precio_compra ?? 0,
                'precio_venta' => $producto->precio ?? 0,
            ]);

            if ($producto->stock !== null && $producto->stock >= $cantidad) {
                AlmacenMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'salida',
                    'cantidad' => $cantidad,
                    'nota' => "Orden #{$orden->numero_orden}",
                ]);
            }

            $orden->calcularTotales();

            return $orden->fresh(['detallesPiezas']);
        });
    }

    public function eliminarPieza(int $ordenId, int $detalleId): void
    {
        DB::transaction(function () use ($ordenId, $detalleId) {
            $detalle = DetallePiezaReparacion::where('orden_reparacion_id', $ordenId)->findOrFail($detalleId);

            $producto = $detalle->producto;
            if ($producto && $producto->stock !== null) {
                AlmacenMovimiento::create([
                    'producto_id' => $producto->id,
                    'tipo' => 'entrada',
                    'cantidad' => $detalle->cantidad,
                    'nota' => "Devuelta desde Orden #{$detalle->ordenReparacion->numero_orden}",
                ]);
            }

            $detalle->delete();

            $detalle->ordenReparacion->calcularTotales();
        });
    }

    public function cambiarEstado(int $orderId, string $nuevoEstado): OrdenReparacion
    {
        $orden = OrdenReparacion::findOrFail($orderId);
        $orden->update(['estado' => $nuevoEstado]);
        return $orden->fresh();
    }

    public function entregarOrden(int $orderId): OrdenReparacion
    {
        return DB::transaction(function () use ($orderId) {
            $orden = OrdenReparacion::findOrFail($orderId);
            $orden->update([
                'estado' => 'entregado',
                'fecha_entrega_real' => now(),
            ]);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'disponible']);
            }

            return $orden->fresh();
        });
    }

    public function cancelarOrden(int $orderId, string $motivo = ''): OrdenReparacion
    {
        return DB::transaction(function () use ($orderId, $motivo) {
            $orden = OrdenReparacion::findOrFail($orderId);

            foreach ($orden->detallesPiezas as $detalle) {
                $producto = $detalle->producto;
                if ($producto && $producto->stock !== null) {
                    AlmacenMovimiento::create([
                        'producto_id' => $producto->id,
                        'tipo' => 'entrada',
                        'cantidad' => $detalle->cantidad,
                        'nota' => "Devuelta por cancelación Orden #{$orden->numero_orden}",
                    ]);
                }
            }

            $orden->update([
                'estado' => 'cancelado',
                'notas' => ($orden->notas ?? '') . ' [CANCELADA: ' . $motivo . ']',
            ]);

            if ($orden->equipo_id) {
                $orden->equipo->update(['estado' => 'disponible']);
            }

            return $orden->fresh();
        });
    }

    public function getOrdenCompleta(int $orderId): OrdenReparacion
    {
        return OrdenReparacion::with([
            'cliente',
            'equipo',
            'tecnico',
            'user',
            'detallesPiezas.producto',
        ])->findOrFail($orderId);
    }

    public function getEstadisticas(array $filters = []): array
    {
        $query = OrdenReparacion::query();

        if (isset($filters['desde'])) {
            $query->whereDate('fecha_recibo', '>=', $filters['desde']);
        }
        if (isset($filters['hasta'])) {
            $query->whereDate('fecha_recibo', '<=', $filters['hasta']);
        }
        if (isset($filters['tecnico_id'])) {
            $query->where('tecnico_id', $filters['tecnico_id']);
        }

        return [
            'total' => (clone $query)->count(),
            'pendientes' => (clone $query)->whereIn('estado', ['recibido', 'pendiente'])->count(),
            'en_reparacion' => (clone $query)->where('estado', 'en_reparacion')->count(),
            'listos' => (clone $query)->where('estado', 'terminado')->count(),
            'entregadas' => (clone $query)->where('estado', 'entregado')->count(),
            'canceladas' => (clone $query)->where('estado', 'cancelado')->count(),
            'ingresos_periodo' => (clone $query)->where('estado', 'entregado')->sum('total'),
            'promedio_tiempo_horas' => (clone $query)->whereNotNull('fecha_entrega_real')->avg('tiempo_reparacion') ?? 0,
        ];
    }
}
