<?php

namespace App\Services;

use App\Models\PlantaGasto;
use Illuminate\Support\Facades\Auth;

class PlantaGastoService
{
    public function list(array $filters = [])
    {
        $query = PlantaGasto::query()->latest();

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('categoria', 'like', "%{$search}%");
            });
        }

        if (isset($filters['activo'])) {
            $query->where('activo', $filters['activo']);
        }

        $plantillas = $query->paginate(20);
        $categorias = PlantaGasto::categorias();
        $totalActivas = PlantaGasto::where('activo', true)->count();
        $totalInactivas = PlantaGasto::where('activo', false)->count();

        return compact('plantillas', 'categorias', 'totalActivas', 'totalInactivas');
    }

    public function create(array $data): PlantaGasto
    {
        $data['tenant_id'] = Auth::user()->business_instance_id ?? null;
        return PlantaGasto::create($data);
    }

    public function update(PlantaGasto $plantilla, array $data): PlantaGasto
    {
        $plantilla->update($data);
        return $plantilla;
    }

    public function delete(PlantaGasto $plantilla): void
    {
        $plantilla->delete();
    }

    public function getActivas(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return PlantaGasto::activas()
            ->orderBy('nombre')
            ->paginate(20);
    }

    public function getCategorias(): array
    {
        return PlantaGasto::categorias();
    }

    public function getMetodosPago(): array
    {
        return PlantaGasto::metodosPago();
    }
}
