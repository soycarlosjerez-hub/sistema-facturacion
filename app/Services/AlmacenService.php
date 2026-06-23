<?php

namespace App\Services;

use App\Models\Almacen;
use App\Models\AlmacenMovimiento;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AlmacenService
{
    public function listarAlmacenes(): LengthAwarePaginator
    {
        $query = Almacen::with('sucursal');

        if (!$this->isAdmin() && $sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        return $query->latest()->paginate(10);
    }

    public function createAlmacen(array $data): Almacen
    {
        return Almacen::create($data);
    }

    public function updateAlmacen(Almacen $almacen, array $data): Almacen
    {
        $almacen->update($data);
        return $almacen;
    }

    public function deleteAlmacen(Almacen $almacen): void
    {
        $almacen->delete();
    }

    public function listarMovimientos(array $filters = []): LengthAwarePaginator
    {
        $query = AlmacenMovimiento::with(['producto', 'almacen', 'user']);

        if (!$this->isAdmin() && $sucursalId = session('sucursal_id')) {
            $query->whereHas('almacen', fn($q) => $q->where('sucursal_id', $sucursalId));
        }

        if ($producto = $filters['producto'] ?? null) {
            $query->whereHas('producto', fn($q) =>
                $q->where('nombre', 'like', "%{$producto}%")
            );
        }

        if ($almacenId = $filters['almacen'] ?? null) {
            $query->where('almacen_id', $almacenId);
        }

        if ($desde = $filters['desde'] ?? null) {
            $query->whereDate('created_at', '>=', $desde);
        }

        if ($hasta = $filters['hasta'] ?? null) {
            $query->whereDate('created_at', '<=', $hasta);
        }

        return $query->latest()->paginate(10);
    }

    public function storeMovimiento(array $data): array
    {
        $isAdmin = $this->isAdmin();
        $sucursalId = session('sucursal_id');

        if ($data['tipo'] === 'traslado') {
            return $this->storeTraslado($data, $isAdmin, $sucursalId);
        }

        $almacen = Almacen::findOrFail($data['almacen_id']);

        if (!$isAdmin && (!$sucursalId || $almacen->sucursal_id !== (int)$sucursalId)) {
            return [
                'success' => false,
                'error'   => 'Solo puedes gestionar movimientos en almacenes de tu sucursal.',
            ];
        }

        if ($data['tipo'] === 'salida') {
            $stockActual = $this->calcularStock($data['producto_id'], $data['almacen_id']);
            if ($stockActual < $data['cantidad']) {
                return [
                    'success' => false,
                    'error'   => "Stock insuficiente en este almacén. Disponible: {$stockActual}, solicitado: {$data['cantidad']}.",
                ];
            }
        }

        DB::beginTransaction();
        try {
            AlmacenMovimiento::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'producto_id' => $data['producto_id'],
                'almacen_id'  => $data['almacen_id'],
                'user_id'     => Auth::id(),
                'tipo'        => $data['tipo'],
                'cantidad'    => $data['cantidad'],
                'nota'        => $data['nota'] ?? null,
            ]);

            $producto = Producto::findOrFail($data['producto_id']);
            $data['tipo'] === 'entrada'
                ? $producto->increment('stock', $data['cantidad'])
                : $producto->decrement('stock', $data['cantidad']);

            DB::commit();

            return ['success' => true, 'message' => 'Movimiento registrado correctamente.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function storeTraslado(array $data, bool $isAdmin, $sucursalId): array
    {
        $almacenOrigen = Almacen::findOrFail($data['almacen_origen_id']);

        if (!$isAdmin && (!$sucursalId || $almacenOrigen->sucursal_id !== (int)$sucursalId)) {
            return [
                'success' => false,
                'error'   => 'Solo puedes gestionar movimientos en almacenes de tu sucursal.',
            ];
        }

        $stockActual = $this->calcularStock($data['producto_id'], $data['almacen_origen_id']);
        if ($stockActual < $data['cantidad']) {
            return [
                'success' => false,
                'error'   => "Stock insuficiente en el almacén de origen. Disponible: {$stockActual}, solicitado: {$data['cantidad']}.",
            ];
        }

        $almacenDestino = Almacen::find($data['almacen_destino_id']);

        DB::beginTransaction();
        try {
            AlmacenMovimiento::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'producto_id' => $data['producto_id'],
                'almacen_id'  => $data['almacen_origen_id'],
                'user_id'     => Auth::id(),
                'tipo'        => 'salida',
                'cantidad'    => $data['cantidad'],
                'nota'        => ($data['nota'] ?? null) ?: 'Traslado a ' . $almacenDestino->nombre,
            ]);

            AlmacenMovimiento::create([
                'tenant_id'   => Auth::user()->business_instance_id,
                'producto_id' => $data['producto_id'],
                'almacen_id'  => $data['almacen_destino_id'],
                'user_id'     => Auth::id(),
                'tipo'        => 'entrada',
                'cantidad'    => $data['cantidad'],
                'nota'        => ($data['nota'] ?? null) ?: 'Traslado desde ' . $almacenOrigen->nombre,
            ]);

            DB::commit();
            return ['success' => true, 'message' => 'Traslado registrado correctamente.'];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function almacenesSegunSucursal(): Collection
    {
        $query = Almacen::orderBy('nombre');

        if (!$this->isAdmin() && $sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        return $query->get();
    }

    public function getStocksData(): array
    {
        $stocks = AlmacenMovimiento::query()
            ->selectRaw('producto_id, almacen_id, GREATEST(SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END), 0) as stock')
            ->groupBy('producto_id', 'almacen_id')
            ->get()
            ->groupBy('producto_id');

        $stocksData = [];
        foreach ($stocks as $productoId => $items) {
            foreach ($items as $item) {
                $stocksData[$productoId][$item->almacen_id] = (int) $item->stock;
            }
        }

        return $stocksData;
    }

    public function inventarioAlmacen(?int $almacenId = null, ?string $buscar = null): array
    {
        $almacenes = Almacen::when(
            !$this->isAdmin() && ($sucursalId = session('sucursal_id')),
            fn($q) => $q->where('sucursal_id', $sucursalId)
        )->orderBy('nombre')->get();

        $stocks = AlmacenMovimiento::query()
            ->selectRaw('producto_id, almacen_id, GREATEST(SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END), 0) as stock')
            ->groupBy('producto_id', 'almacen_id')
            ->get()
            ->groupBy('almacen_id');

        $productos = Producto::orderBy('nombre')->get();

        return compact('almacenes', 'almacenId', 'buscar', 'stocks', 'productos');
    }

    public function calcularStock(int $productoId, int $almacenId): int
    {
        return (int) AlmacenMovimiento::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->selectRaw('GREATEST(SUM(CASE WHEN tipo="entrada" THEN cantidad ELSE -cantidad END), 0) as stock')
            ->value('stock');
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
