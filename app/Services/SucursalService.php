<?php

namespace App\Services;

use App\Models\Sucursal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SucursalService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Sucursal::withCount(['almacenes', 'cajas', 'usuarios']);

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('rnc', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('nombre')->paginate(10)->appends($filters);
    }

    public function create(array $data): Sucursal
    {
        $data['activa'] = $data['activa'] ?? false;
        $data['es_matriz'] = $data['es_matriz'] ?? false;
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;

        // Si es matriz, quitar matriz a las demás
        if ($data['es_matriz']) {
            Sucursal::where('es_matriz', true)->update(['es_matriz' => false]);
        }

        return Sucursal::create($data);
    }

    public function update(Sucursal $sucursal, array $data): Sucursal
    {
        $data['activa'] = $data['activa'] ?? false;
        $data['es_matriz'] = $data['es_matriz'] ?? false;

        // Si es matriz, quitar matriz a las demás
        if ($data['es_matriz']) {
            Sucursal::where('id', '!=', $sucursal->id)->where('es_matriz', true)->update(['es_matriz' => false]);
        }

        $sucursal->update($data);

        return $sucursal;
    }

    public function delete(Sucursal $sucursal): array
    {
        $relations = [
            'ventas' => 'ventas',
            'compras' => 'compras',
            'cajas' => 'cajas',
            'gastos' => 'gastos',
            'conduces' => 'conduces',
            'cotizaciones' => 'cotizaciones',
            'almacenes' => 'almacenes',
            'usuarios' => 'usuarios',
        ];

        $blockers = [];
        foreach ($relations as $key => $relation) {
            $count = $sucursal->{$relation}()->count();
            if ($count > 0) {
                $blockers[] = "{$count} {$key}";
            }
        }

        if (!empty($blockers)) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar: tiene ' . implode(', ', $blockers) . ' asociados.',
            ];
        }

        $sucursal->delete();

        return ['success' => true, 'message' => 'Sucursal eliminada exitosamente.'];
    }

    public function getStats(Sucursal $sucursal): array
    {
        $ventasMes = $sucursal->ventas()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('estado', ['completada', 'pagada']);

        $ventasMesAnt = $sucursal->ventas()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereIn('estado', ['completada', 'pagada']);

        return [
            'ventas_mes' => (clone $ventasMes)->count(),
            'ingresos_mes' => (clone $ventasMes)->sum('total'),
            'ventas_mes_ant' => (clone $ventasMesAnt)->count(),
            'compras_mes' => $sucursal->compras()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'cajas_activas' => $sucursal->cajas()->where('estado', 'abierta')->count(),
            'empleados' => $sucursal->usuarios()->count(),
            'almacenes' => $sucursal->almacenes()->count(),
        ];
    }

    public function getRecentActivity(Sucursal $sucursal): array
    {
        return [
            'ultimas_ventas' => $sucursal->ventas()
                ->with('cliente:id,nombre', 'usuario:id,name')
                ->latest()
                ->take(5)
                ->get(),
        ];
    }
}
