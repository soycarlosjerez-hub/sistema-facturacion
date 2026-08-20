<?php

namespace App\Services;

use App\Models\Presupuesto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PresupuestoService
{
    public function list(array $filters = []): array
    {
        $query = Presupuesto::query();
        $query = $this->applyFilters($query, $filters);

        $presupuestos = $query->with(['cliente', 'items.producto'])->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Presupuesto::count(),
            'borrador' => Presupuesto::where('estado', 'borrador')->count(),
            'enviado' => Presupuesto::where('estado', 'enviado')->count(),
            'aceptado' => Presupuesto::where('estado', 'aceptado')->count(),
            'rechazado' => Presupuesto::where('estado', 'rechazado')->count(),
            'monto_total' => Presupuesto::where('estado', '!=', 'cancelado')->sum('total'),
            'por_vencer' => Presupuesto::porVencer()->count(),
        ];

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre', 'documento']);
        $estados = [
            'borrador'    => 'Borrador',
            'enviado'     => 'Enviado',
            'aceptado'    => 'Aceptado',
            'rechazado'   => 'Rechazado',
            'convertido'  => 'Convertido a Venta',
            'cancelado'   => 'Cancelado',
        ];

        return compact('presupuestos', 'stats', 'clientes', 'estados');
    }

    public function buildQuery(Request $request): Builder
    {
        $query = Presupuesto::query();
        return $this->applyFilters($query, $request->all());
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['cliente'])) {
            $query->where('cliente_id', $filters['cliente']);
        }

        if (isset($filters['estado']) && $filters['estado'] !== '') {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['buscar'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('numero', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhereHas('cliente', fn($cq) => $cq->where('nombre', 'like', '%' . $filters['buscar'] . '%'));
            });
        }

        if (!empty($filters['desde'])) {
            $query->where('created_at', '>=', $filters['desde']);
        }

        if (!empty($filters['hasta'])) {
            $query->where('created_at', '<=', $filters['hasta']);
        }

        return $query;
    }

    public function create(array $data): Presupuesto
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;
        $data['creado_por'] = Auth::id();
        $data['numero'] = Presupuesto::generarNumero();
        $data['estado'] = $data['estado'] ?? 'borrador';

        return Presupuesto::create($data);
    }

    public function update(Presupuesto $presupuesto, array $data): Presupuesto
    {
        $presupuesto->update($data);

        if (isset($data['items'])) {
            $this->syncItems($presupuesto, $data['items']);
        }

        $presupuesto->calcularTotales();
        return $presupuesto;
    }

    public function delete(Presupuesto $presupuesto): void
    {
        if (in_array($presupuesto->estado, ['aceptado', 'convertido'])) {
            throw new \Exception("No se puede eliminar un presupuesto {$presupuesto->estado_label}.");
        }

        $presupuesto->items()->delete();
        $presupuesto->delete();
    }

    public function cambiarEstado(Presupuesto $presupuesto, string $nuevoEstado): array
    {
        if ($nuevoEstado === 'aceptado' && $presupuesto->estado === 'aceptado') {
            return ['success' => false, 'message' => 'El presupuesto ya se encuentra aceptado.'];
        }

        if ($nuevoEstado === 'cancelado' && !in_array($presupuesto->estado, ['borrador', 'enviado', 'rechazado'])) {
            return ['success' => false, 'message' => 'No se puede cancelar un presupuesto en este estado.'];
        }

        $presupuesto->update(['estado' => $nuevoEstado]);

        return [
            'success' => true,
            'message' => "Estado actualizado a: {$presupuesto->estado_label}",
        ];
    }

    public function convertirEnVenta(Presupuesto $presupuesto): array
    {
        if ($presupuesto->estado !== 'aceptado') {
            return ['success' => false, 'message' => 'Solo se pueden convertir presupuestos aceptados.'];
        }

        if ($presupuesto->estado === 'convertido') {
            return ['success' => false, 'message' => 'Este presupuesto ya fue convertido a venta.'];
        }

        try {
            DB::beginTransaction();

            // First, check if there's a Venta model
            $ventaClass = \App\Models\Venta::class;
            if (!class_exists($ventaClass)) {
                return ['success' => false, 'message' => 'El modelo Venta no existe. No se puede convertir.'];
            }

            // Find products in items that have producto_id and create venta detalles
            $venta = $ventaClass::create([
                'cliente_id' => $presupuesto->cliente_id,
                'subtotal'   => (float) $presupuesto->subtotal,
                'impuestos'  => (float) $presupuesto->itbis,
                'descuento'  => (float) $presupuesto->descuento,
                'total'      => (float) $presupuesto->total,
                'notas'      => $presupuesto->notas . ' (Convertido desde presupuesto #' . $presupuesto->numero . ')',
                'tenant_id'  => $presupuesto->tenant_id,
            ]);

            foreach ($presupuesto->items as $item) {
                if ($item->producto_id && class_exists(\App\Models\VentaDetalle::class)) {
                    \App\Models\VentaDetalle::create([
                        'venta_id'         => $venta->id,
                        'producto_id'      => $item->producto_id,
                        'cantidad'         => $item->cantidad,
                        'precio_unitario'  => $item->precio_unitario,
                        'subtotal'         => $item->subtotal,
                        'tenant_id'        => $presupuesto->tenant_id,
                    ]);
                }
            }

            $presupuesto->update(['estado' => 'convertido']);
            DB::commit();

            return [
                'success' => true,
                'message' => "Presupuesto convertido a venta #{$venta->id}",
                'venta_id' => $venta->id,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Error al convertir: ' . $e->getMessage()];
        }
    }

    public function generatePdfData(Presupuesto $presupuesto): array
    {
        $presupuesto->load(['cliente', 'items.producto', 'user']);
        return [
            'presupuesto' => $presupuesto,
            'empresa' => [
                'nombre' => \App\Models\SystemSetting::nombreActual(),
            ],
        ];
    }

    protected function syncItems(Presupuesto $presupuesto, array $items): void
    {
        foreach ($items as $i => $itemData) {
            $producto = !empty($itemData['producto_id']) ? \App\Models\Producto::find($itemData['producto_id']) : null;

            $presupuesto->items()->create(array_merge([
                'tipo_item'      => $itemData['tipo_item'] ?? 'producto',
                'cantidad'       => $itemData['cantidad'] ?? 1,
                'precio_unitario' => $itemData['precio_unitario'] ?? 0,
                'descuento'      => $itemData['descuento'] ?? 0,
                'itbis_porcentaje' => $itemData['itbis_porcentaje'] ?? ($producto?->itbis_porcentaje ?? \App\Models\SystemSetting::itbisDefault()),
                'descripcion'    => $itemData['descripcion'] ?? ($producto?->nombre ?? 'Item'),
            ], [
                'tenant_id' => auth()->user()->business_instance_id,
            ]));
        }
    }
}
