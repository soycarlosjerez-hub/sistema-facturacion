<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Conduce;
use App\Models\ConduceItem;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ConduceService
{
    public function list(array $filters = []): array
    {
        $query = Conduce::with(['cliente', 'user', 'items']);

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($estado = $filters['estado'] ?? null) {
            $query->where('estado', $estado);
        }
        if ($clienteId = $filters['cliente_id'] ?? null) {
            $query->where('cliente_id', $clienteId);
        }
        if ($desde = $filters['fecha_desde'] ?? null) {
            $query->whereDate('fecha', '>=', $desde);
        }
        if ($hasta = $filters['fecha_hasta'] ?? null) {
            $query->whereDate('fecha', '<=', $hasta);
        }
        if ($q = $filters['q'] ?? null) {
            $query->buscar($q);
        }

        $conduces = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $stats = [
            'total'          => Conduce::count(),
            'borrador'       => Conduce::where('estado', 'borrador')->count(),
            'en_transito'    => Conduce::where('estado', 'en_transito')->count(),
            'entregados'     => Conduce::where('estado', 'entregado')->count(),
            'entregados_hoy' => Conduce::where('estado', 'entregado')->whereDate('fecha_recibido', today())->count(),
            'vencidos'       => Conduce::where('estado', 'vencido')->count(),
        ];

        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre', 'rnc_cedula']);
        $estados = Conduce::ESTADOS;

        return compact('conduces', 'stats', 'clientes', 'estados');
    }

    public function getCreateData(?int $fromVenta = null): array
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida'])
            ->each(fn($p) => $p->unidad = $p->unidad_medida ?? 'UND');
        $venta = $fromVenta ? Venta::with('detalles.producto')->findOrFail($fromVenta) : null;

        return compact('clientes', 'productos', 'venta');
    }

    public function create(array $data): Conduce
    {
        try {
            DB::beginTransaction();

            $conduce = Conduce::create([
                'numero'            => Conduce::generarNumero(),
                'fecha'             => $data['fecha'],
                'fecha_entrega'     => $data['fecha_entrega'] ?? null,
                'cliente_id'        => $data['cliente_id'],
                'user_id'           => Auth::id(),
                'sucursal_id'       => session('sucursal_id'),
                'tenant_id'         => Auth::user()->business_instance_id ?? null,
                'venta_id'          => $data['venta_id'] ?? null,
                'direccion_entrega' => $data['direccion_entrega'],
                'referencia'        => $data['referencia'] ?? null,
                'contacto_entrega'  => $data['contacto_entrega'] ?? null,
                'telefono_entrega'  => $data['telefono_entrega'] ?? null,
                'transportista'     => $data['transportista'] ?? null,
                'vehiculo'          => $data['vehiculo'] ?? null,
                'placa'             => $data['placa'] ?? null,
                'chofer'            => $data['chofer'] ?? null,
                'chofer_cedula'     => $data['chofer_cedula'] ?? null,
                'observaciones'     => $data['observaciones'] ?? null,
                'estado'            => $data['estado'] ?? 'borrador',
            ]);

            $this->syncItems($conduce, $data['items']);

            DB::commit();

            return $conduce;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error creando conduce: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(Conduce $conduce, array $data): Conduce
    {
        if ($conduce->estado === 'entregado') {
            abort(400, 'No se puede modificar un conduce ya entregado.');
        }

        try {
            DB::beginTransaction();

            $conduce->update([
                'fecha'             => $data['fecha'],
                'fecha_entrega'     => $data['fecha_entrega'] ?? null,
                'cliente_id'        => $data['cliente_id'],
                'venta_id'          => $data['venta_id'] ?? null,
                'direccion_entrega' => $data['direccion_entrega'],
                'referencia'        => $data['referencia'] ?? null,
                'contacto_entrega'  => $data['contacto_entrega'] ?? null,
                'telefono_entrega'  => $data['telefono_entrega'] ?? null,
                'transportista'     => $data['transportista'] ?? null,
                'vehiculo'          => $data['vehiculo'] ?? null,
                'placa'             => $data['placa'] ?? null,
                'chofer'            => $data['chofer'] ?? null,
                'chofer_cedula'     => $data['chofer_cedula'] ?? null,
                'observaciones'     => $data['observaciones'] ?? null,
                'estado'            => $data['estado'] ?? $conduce->estado,
            ]);

            $conduce->items()->delete();
            $this->syncItems($conduce, $data['items']);

            DB::commit();

            return $conduce;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error actualizando conduce: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(Conduce $conduce): void
    {
        if ($conduce->estado === 'entregado') {
            abort(400, 'No se puede eliminar un conduce ya entregado.');
        }

        try {
            $conduce->delete();
        } catch (\Throwable $e) {
            Log::error('Error eliminando conduce: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getEditData(Conduce $conduce): array
    {
        if ($conduce->estado === 'entregado') {
            abort(400, 'No se puede editar un conduce ya entregado.');
        }

        $conduce->load('items');
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida'])
            ->each(fn($p) => $p->unidad = $p->unidad_medida ?? 'UND');

        return compact('conduce', 'clientes', 'productos');
    }

    public function cambiarEstado(Conduce $conduce, string $estado): void
    {
        $conduce->cambiarEstado($estado);
    }

    public function entregar(Conduce $conduce, string $recibidoPor, ?string $cedula, array $itemsRecibidos): void
    {
        $conduce->marcarEntregado($recibidoPor, $cedula, $itemsRecibidos);
    }

    public function fromVenta(Venta $venta): array
    {
        $venta->load('detalles.producto', 'cliente');
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get(['id', 'nombre', 'codigo_barras as codigo', 'stock', 'unidad_medida'])
            ->each(fn($p) => $p->unidad = $p->unidad_medida ?? 'UND');

        $prefillItems = $venta->detalles->map(fn($d) => [
            'producto_id' => $d->producto_id,
            'nombre'      => $d->producto?->nombre ?? $d->nombre ?? 'Producto',
            'codigo'      => $d->producto?->codigo_barras,
            'cantidad'    => (float) $d->cantidad,
            'unidad'      => $d->producto?->unidad_medida ?? 'UND',
            'peso'        => 0,
        ])->toArray();

        return compact('clientes', 'productos', 'venta', 'prefillItems');
    }

    protected function syncItems(Conduce $conduce, array $items): void
    {
        foreach ($items as $idx => $item) {
            $conduce->items()->create([
                'producto_id' => $item['producto_id'] ?? null,
                'nombre'      => $item['nombre'],
                'codigo'      => $item['codigo'] ?? null,
                'cantidad'    => $item['cantidad'],
                'unidad'      => $item['unidad'] ?? 'UND',
                'peso'        => $item['peso'] ?? 0,
                'orden'       => $idx,
                'tenant_id'   => Auth::user()->business_instance_id,
            ]);
        }

        $conduce->calcularTotales();
    }
}
