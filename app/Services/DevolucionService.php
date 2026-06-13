<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Producto;
use App\Models\Venta;
use App\Services\Ecf\EcfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class DevolucionService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Devolucion::with(['venta', 'cliente', 'user', 'detalles.producto']);

        if ($cliente = $filters['cliente'] ?? null) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$cliente}%"));
        }
        if ($estado = $filters['estado'] ?? null) {
            $query->where('estado', $estado);
        }
        if ($desde = $filters['fecha_desde'] ?? null) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta = $filters['fecha_hasta'] ?? null) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        return $query->orderBy('id', 'desc')->paginate(20);
    }

    public function getCreateData(?int $ventaId = null): array
    {
        $venta = $ventaId ? Venta::with(['detalles.producto', 'cliente'])->findOrFail($ventaId) : null;
        $clientes = Cliente::orderBy('nombre')->get();

        return compact('venta', 'clientes');
    }

    public function create(array $data): Devolucion
    {
        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itbis = 0;

            foreach ($data['items'] as $item) {
                $base = (float) $item['cantidad'] * (float) $item['precio_unitario'];
                $imp = $base * ((float) ($item['itbis_porcentaje'] ?? 18) / 100);
                $subtotal += $base;
                $itbis += $imp;
            }

            $total = $subtotal + $itbis;

            $devolucion = Devolucion::create([
                'codigo'    => Devolucion::generarCodigo(),
                'venta_id'  => $data['venta_id'] ?? null,
                'cliente_id' => $data['cliente_id'],
                'user_id'   => Auth::id(),
                'fecha'     => $data['fecha'],
                'motivo'    => $data['motivo'],
                'tipo'      => $data['tipo'],
                'subtotal'  => round($subtotal, 2),
                'itbis'     => round($itbis, 2),
                'total'     => round($total, 2),
                'estado'    => 'borrador',
            ]);

            foreach ($data['items'] as $item) {
                DetalleDevolucion::create([
                    'devolucion_id'    => $devolucion->id,
                    'producto_id'      => $item['producto_id'],
                    'cantidad'         => $item['cantidad'],
                    'precio_unitario'  => $item['precio_unitario'],
                    'itbis_porcentaje' => $item['itbis_porcentaje'] ?? 18,
                    'subtotal'         => round((float) $item['cantidad'] * (float) $item['precio_unitario'] * (1 + ((float) ($item['itbis_porcentaje'] ?? 18) / 100)), 2),
                    'motivo'           => $item['motivo'] ?? null,
                ]);
            }

            DB::commit();
            return $devolucion;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmar(Devolucion $devolucion): Devolucion
    {
        if ($devolucion->estado !== 'borrador') {
            throw new \Exception('Solo se pueden confirmar devoluciones en estado borrador.');
        }

        DB::beginTransaction();
        try {
            foreach ($devolucion->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                }
            }

            $devolucion->update(['estado' => 'completada']);
            DB::commit();

            return $devolucion;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function generarNotaCredito(Devolucion $devolucion)
    {
        if ($devolucion->estado !== 'completada') {
            throw new \Exception('La devolución debe estar completada para generar una Nota de Crédito.');
        }
        if ($devolucion->nota_credito_id) {
            throw new \Exception('Ya se generó una Nota de Crédito para esta devolución.');
        }
        if (!$devolucion->tiene_ecf) {
            throw new \Exception('La venta asociada no es un e-CF. No se puede generar Nota de Crédito electrónica.');
        }

        $ecfOriginal = $devolucion->venta?->ecfDocumento;
        if (!$ecfOriginal) {
            throw new \Exception('La venta no tiene un e-CF asociado.');
        }

        $ecfService = app(EcfService::class);
        $notaCredito = $ecfService->generarE34PorDevolucion($ecfOriginal, $devolucion);

        $devolucion->update(['nota_credito_id' => $notaCredito->id]);

        return $notaCredito;
    }

    public function delete(Devolucion $devolucion): void
    {
        if ($devolucion->estado === 'completada') {
            throw new \Exception('No se puede eliminar una devolución completada. Anúlala en su lugar.');
        }
        $devolucion->detalles()->delete();
        $devolucion->delete();
    }

    public function buscarVenta(string $term)
    {
        return Venta::where('id', 'like', "%{$term}%")
            ->orWhereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$term}%"))
            ->with(['cliente', 'detalles.producto'])
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id'    => $v->id,
                'label' => "Venta #{$v->id} - {$v->cliente?->nombre}",
                'total' => $v->total,
                'fecha' => $v->created_at->format('d/m/Y'),
                'detalles' => $v->detalles->map(fn($d) => [
                    'producto_id'     => $d->producto_id,
                    'producto_nombre' => $d->producto->nombre ?? 'N/A',
                    'cantidad'        => $d->cantidad,
                    'precio'          => $d->precio_unitario,
                    'itbis'           => $d->itbis_porcentaje ?? 18,
                ]),
            ]);
    }
}
