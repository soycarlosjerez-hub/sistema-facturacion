<?php

namespace App\Services;

use App\Enums\OrdenState;
use App\Enums\OrdenTipo;
use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Cliente;
use App\Models\Orden;
use App\Models\OrdenDetalle;
use App\Models\Producto;
use App\Models\SesionCaja;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrdenService
{
    public function getIndexData(): array
    {
        $query = Orden::deSucursal()->with(['detalles.producto', 'cliente', 'usuario', 'terminal', 'entregaEmpresa']);

        return [
            'ordenes' => $query->orderBy('created_at', 'desc')->paginate(15),
            'totales' => [
                'pendientes' => (clone $query)->whereIn('estado', ['pendiente', 'confirmada'])->count(),
                'en_proceso' => (clone $query)->where('estado', 'en_proceso')->count(),
                'hoy'        => (clone $query)->whereDate('created_at', today())->sum('total'),
            ],
        ];
    }

    public function createOrden(array $data): Orden
    {
        $user = Auth::user();

        $data['user_id'] = $user->id;
        $data['tenant_id'] = $user->business_instance_id;
        $data['sucursal_id'] = $user->sucursal_id ?? session('sucursal_id');

        $sesion = SesionCaja::where('user_id', $user->id)
            ->where('estado', 'abierta')
            ->latest('fecha_apertura')
            ->first();

        if ($sesion) {
            $data['caja_id'] = $sesion->caja_id;
            $data['sesion_caja_id'] = $sesion->id;
        }

        $data['subtotal'] = 0;
        $data['impuestos'] = 0;
        $data['estado'] = OrdenState::Pendiente->value;

        if (empty($data['cliente_id'])) {
            $data['cliente_id'] = Cliente::consumidorFinal()->id ?? null;
        }

        return Orden::create($data);
    }

    public function agregarItem(Orden $orden, int $productoId, int $cantidad, ?string $notas, ?string $curso): array
    {
        $producto = Producto::findOrFail($productoId);
        $curso = $curso ?? 'fuerte';

        $detalleExistente = OrdenDetalle::where('orden_id', $orden->id)
            ->where('producto_id', $producto->id)
            ->where('notas', $notas)
            ->where('curso', $curso)
            ->first();

        DB::beginTransaction();
        try {
            if ($detalleExistente) {
                $nuevaCantidad = $detalleExistente->cantidad + $cantidad;
                $detalleExistente->cantidad = $nuevaCantidad;
                $detalleExistente->subtotal = $producto->precio * $nuevaCantidad;
                $detalleExistente->save();
            } else {
                $detalleExistente = OrdenDetalle::create([
                    'orden_id'        => $orden->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $producto->precio * $cantidad,
                    'notas'           => $notas,
                    'curso'           => $curso,
                    'estado_cocina'   => 'pendiente',
                    'tenant_id'       => $orden->tenant_id,
                ]);
            }

            $itbisItem = ($producto->itbis_porcentaje ?? 0) / 100 * $producto->precio * $cantidad;
            $orden->increment('subtotal', $producto->precio * $cantidad);
            $orden->increment('impuestos', $itbisItem);

            // Descontar stock
            $producto->decrement('stock', $cantidad);

            foreach ($producto->ingredientes as $ingrediente) {
                $ingrediente->decrement('stock', $ingrediente->pivot->cantidad * $cantidad);
            }

            DB::commit();

            return [
                'orden'   => $orden->fresh()->load('detalles.producto'),
                'detalle' => $detalleExistente->fresh()->load('producto'),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function quitarItem(Orden $orden, OrdenDetalle $detalle): array
    {
        if ($detalle->orden_id !== $orden->id) {
            return ['error' => 'El detalle no pertenece a esta orden', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            $subtotal = $detalle->subtotal;
            $itbisItem = ($detalle->producto->itbis_porcentaje ?? 0) / 100 * $subtotal;

            $orden->decrement('subtotal', $subtotal);
            $orden->decrement('impuestos', $itbisItem);

            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
                foreach ($detalle->producto->ingredientes as $ingrediente) {
                    $ingrediente->increment('stock', $ingrediente->pivot->cantidad * $detalle->cantidad);
                }
            }

            $detalle->delete();
            DB::commit();

            return ['orden' => $orden->fresh()->load('detalles.producto')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function actualizarItem(Orden $orden, OrdenDetalle $detalle, int $nuevaCantidad): array
    {
        if ($detalle->orden_id !== $orden->id) {
            return ['error' => 'El detalle no pertenece a esta orden', 'code' => 422];
        }

        $precioUnitario = (float) $detalle->precio_unitario;
        $diferencia = $nuevaCantidad - $detalle->cantidad;
        $producto = $detalle->producto;

        DB::beginTransaction();
        try {
            $nuevoSubtotal = $precioUnitario * $nuevaCantidad;
            $itbisPorcentaje = ($producto->itbis_porcentaje ?? 0) / 100;
            $itbisAnterior = $detalle->subtotal * $itbisPorcentaje;
            $itbisNuevo = $nuevoSubtotal * $itbisPorcentaje;

            $orden->increment('subtotal', $nuevoSubtotal - $detalle->subtotal);
            $orden->increment('impuestos', $itbisNuevo - $itbisAnterior);

            $detalle->cantidad = $nuevaCantidad;
            $detalle->subtotal = $nuevoSubtotal;
            $detalle->save();

            if ($producto) {
                $producto->decrement('stock', $diferencia);
                foreach ($producto->ingredientes as $ingrediente) {
                    $ingrediente->decrement('stock', $ingrediente->pivot->cantidad * $diferencia);
                }
            }

            DB::commit();

            return ['orden' => $orden->fresh()->load('detalles.producto')];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function aplicarDescuento(Orden $orden, string $tipo, float $valor, string $motivo): array
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole('admin');

        if ($tipo === 'porcentaje') {
            if ($valor > 50 && !$isAdmin) {
                return ['error' => 'Descuento mayor a 50% requiere autorización de administrador', 'code' => 422];
            }
            $descuento = $orden->subtotal * ($valor / 100);
        } else {
            if ($valor > $orden->subtotal) {
                return ['error' => 'El descuento no puede exceder el subtotal', 'code' => 422];
            }
            $maxAuto = $orden->subtotal * 0.3;
            if ($valor > $maxAuto && !$isAdmin) {
                return ['error' => 'Descuento mayor a 30% requiere autorización de administrador', 'code' => 422];
            }
            $descuento = $valor;
        }

        $descuento = round($descuento, 2);
        $nuevoSubtotal = round($orden->subtotal - $descuento, 2);
        $nuevoTotal = round($nuevoSubtotal + $orden->impuestos, 2);

        $orden->update([
            'subtotal'         => $nuevoSubtotal,
            'descuento'        => $descuento,
            'descuento_tipo'   => $tipo,
            'descuento_motivo' => $motivo,
        ]);

        $orden->load('detalles.producto', 'cliente', 'usuario');
        return ['success' => true, 'orden' => $orden];
    }

    public function anular(Orden $orden, string $motivo): array
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && $orden->detalles->sum('subtotal') > 500) {
            return ['error' => 'Se requiere autorización de administrador para anular órdenes mayores a RD$500', 'code' => 422];
        }

        DB::beginTransaction();
        try {
            foreach ($orden->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                }
            }

            $orden->update(['estado' => OrdenState::Anulada->value, 'notas' => $motivo]);
            DB::commit();

            return ['success' => true];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Error al anular: ' . $e->getMessage(), 'code' => 500];
        }
    }

    public function cambiarEstado(Orden $orden, string $nuevoEstado): array
    {
        $transiciones = [
            OrdenState::Pendiente->value => [OrdenState::Confirmada->value, OrdenState::EnProceso->value, OrdenState::Anulada->value],
            OrdenState::Confirmada->value => [OrdenState::EnProceso->value, OrdenState::Anulada->value],
            OrdenState::EnProceso->value => [OrdenState::Lista->value, OrdenState::EnCamino->value, OrdenState::Anulada->value],
            OrdenState::Lista->value => [OrdenState::Recogida->value, OrdenState::Entregado->value],
            OrdenState::EnCamino->value => [OrdenState::Entregado->value],
            OrdenState::Recogida->value => [OrdenState::Completada->value],
            OrdenState::Entregado->value => [OrdenState::Completada->value],
        ];

        $actual = $orden->estado;
        if (!isset($transiciones[$actual]) || !in_array($nuevoEstado, $transiciones[$actual])) {
            return ['error' => "No se puede cambiar de '$actual' a '$nuevoEstado'", 'code' => 422];
        }

        $orden->update(['estado' => $nuevoEstado]);
        return ['success' => true, 'orden' => $orden->fresh()->load('detalles.producto', 'cliente')];
    }
}
