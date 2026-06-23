<?php

namespace App\Services;

use App\Models\Gasto;
use App\Models\SesionCaja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class GastoService
{
    public function list(array $filters = []): array
    {
        $query = Gasto::with('user')->latest('fecha_gasto');

        if ($sucursalId = session('sucursal_id')) {
            $query->where('sucursal_id', $sucursalId);
        }

        if ($categoria = $filters['categoria'] ?? null) {
            $query->ofCategoria($categoria);
        }
        if ($desde = $filters['desde'] ?? null) {
            $query->whereDate('fecha_gasto', '>=', $desde);
        }
        if ($hasta = $filters['hasta'] ?? null) {
            $query->whereDate('fecha_gasto', '<=', $hasta);
        }
        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', "%{$search}%")
                  ->orWhere('notas', 'like', "%{$search}%")
                  ->orWhere('comprobante', 'like', "%{$search}%");
            });
        }

        $gastos = $query->paginate(20);
        $totalGastos = $query->sum('monto');
        $categorias = Gasto::categorias();
        $totalPorCategoria = Gasto::query()
            ->selectRaw('categoria, SUM(monto) as total')
            ->when($desde, fn($q) => $q->whereDate('fecha_gasto', '>=', $desde))
            ->when($hasta, fn($q) => $q->whereDate('fecha_gasto', '<=', $hasta))
            ->groupBy('categoria')
            ->pluck('total', 'categoria');

        return compact('gastos', 'totalGastos', 'categorias', 'totalPorCategoria');
    }

    public function create(array $data): Gasto
    {
        $data['user_id'] = Auth::id();
        $data['sucursal_id'] = session('sucursal_id');
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;

        $sesionActiva = SesionCaja::where('user_id', Auth::id())
            ->where('estado', 'abierta')
            ->latest()
            ->first();
        if ($sesionActiva) {
            $data['caja_id'] = $sesionActiva->caja_id;
            $data['sesion_caja_id'] = $sesionActiva->id;
        }

        return Gasto::create($data);
    }

    public function update(Gasto $gasto, array $data): Gasto
    {
        $gasto->update($data);
        return $gasto;
    }

    public function delete(Gasto $gasto): void
    {
        $gasto->delete();
    }

    public function getCategorias(): array
    {
        return Gasto::categorias();
    }
}
