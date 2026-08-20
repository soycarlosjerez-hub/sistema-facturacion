<?php

namespace App\Services;

use App\Models\MarcaTecnologica;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class MarcaTecnologicaService
{
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = MarcaTecnologica::query();
        $query = $this->applyFilters($query, $filters);

        return $query->latest()->paginate(20)->withQueryString();
    }

    public function buildQuery(Request $request): Builder
    {
        $query = MarcaTecnologica::query();
        return $this->applyFilters($query, $request->all());
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $query->where('activo', filter_var($filters['activo'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['pais'])) {
            $query->where('pais', 'like', '%' . $filters['pais'] . '%');
        }

        return $query;
    }

    public function create(array $data): MarcaTecnologica
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;

        return MarcaTecnologica::create($data);
    }

    public function update(MarcaTecnologica $marcaTecnologica, array $data): MarcaTecnologica
    {
        $marcaTecnologica->update($data);
        return $marcaTecnologica;
    }

    public function delete(MarcaTecnologica $marcaTecnologica): void
    {
        if ($marcaTecnologica->productos()->count() > 0) {
            throw new \Exception('No se puede eliminar: la marca tiene productos asociados.');
        }

        if ($marcaTecnologica->logo_url) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($marcaTecnologica->logo_url);
        }

        $marcaTecnologica->delete();
    }

    public function toggleActivo(MarcaTecnologica $marcaTecnologica): MarcaTecnologica
    {
        $marcaTecnologica->update(['activo' => !$marcaTecnologica->activo]);
        return $marcaTecnologica->fresh();
    }
}
