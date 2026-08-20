<?php

namespace App\Services;

use App\Models\LicenciaSoftware;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class LicenciaSoftwareService
{
    public function list(array $filters = []): array
    {
        $query = LicenciaSoftware::query();
        $query = $this->applyFilters($query, $filters);

        $licencias = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => LicenciaSoftware::count(),
            'activas' => LicenciaSoftware::activas()->count(),
            'por_vencer' => LicenciaSoftware::porVencer()->count(),
            'vencidas' => LicenciaSoftware::vencidas()->count(),
        ];

        $plataformas = LicenciaSoftware::whereNotNull('plataforma')
            ->where('plataforma', '!=', '')
            ->distinct()
            ->pluck('plataforma');

        $tipos = LicenciaSoftware::whereNotNull('tipo_licencia')
            ->where('tipo_licencia', '!=', '')
            ->distinct()
            ->pluck('tipo_licencia');

        return compact('licencias', 'stats', 'plataformas', 'tipos');
    }

    public function buildQuery(Request $request): Builder
    {
        $query = LicenciaSoftware::query();
        return $this->applyFilters($query, $request->all());
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['producto'])) {
            $query->whereHas('producto', function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['producto'] . '%');
            });
        }

        if (!empty($filters['plataforma'])) {
            $query->where('plataforma', $filters['plataforma']);
        }

        if (isset($filters['estado']) && $filters['estado'] !== '') {
            if ($filters['estado'] === 'por-vencer') {
                $query->porVencer();
            } elseif ($filters['estado'] === 'vencida') {
                $query->vencidas();
            } elseif ($filters['estado'] === 'activa') {
                $query->activas();
            } elseif ($filters['estado'] === 'inactiva') {
                $query->where('licencia_activa', false);
            } else {
                $query->where('licencia_activa', filter_var($filters['estado'], FILTER_VALIDATE_BOOLEAN));
            }
        }

        if (isset($filters['licencia_activa']) && $filters['licencia_activa'] !== '') {
            $query->where('licencia_activa', filter_var($filters['licencia_activa'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['usuario'])) {
            $query->porUsuario($filters['usuario']);
        }

        if (!empty($filters['tipo_licencia'])) {
            $query->porTipo($filters['tipo_licencia']);
        }

        return $query;
    }

    public function create(array $data): LicenciaSoftware
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;

        return LicenciaSoftware::create($data);
    }

    public function update(LicenciaSoftware $licencia, array $data): LicenciaSoftware
    {
        $licencia->update($data);
        return $licencia;
    }

    public function delete(LicenciaSoftware $licencia): void
    {
        $licencia->delete();
    }

    public function toggleActiva(LicenciaSoftware $licencia): LicenciaSoftware
    {
        $licencia->update(['licencia_activa' => !$licencia->licencia_activa]);
        return $licencia->fresh();
    }
}
