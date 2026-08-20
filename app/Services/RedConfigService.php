<?php

namespace App\Services;

use App\Models\RedConfig;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class RedConfigService
{
    public function list(array $filters = []): array
    {
        $query = RedConfig::query();
        $query = $this->applyFilters($query, $filters);

        $redes = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => RedConfig::count(),
            'activas' => RedConfig::activas()->count(),
            'con_vlan' => RedConfig::whereNotNull('vlan_id')->count(),
            'dhcp_activado' => RedConfig::where('dhcp_activado', true)->count(),
        ];

        $clientes = RedConfig::whereNotNull('cliente_id')
            ->with('cliente')
            ->distinct()
            ->get()
            ->pluck('cliente', 'cliente_id')
            ->filter();

        $vlans = RedConfig::whereNotNull('vlan_id')
            ->distinct()
            ->pluck('vlan_id')
            ->sort()
            ->values();

        return compact('redes', 'stats', 'clientes', 'vlans');
    }

    public function buildQuery(Request $request): Builder
    {
        $query = RedConfig::query();
        return $this->applyFilters($query, $request->all());
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['cliente'])) {
            $query->where('cliente_id', $filters['cliente']);
        }

        if (isset($filters['estado']) && $filters['estado'] !== '') {
            $query->where('activo', filter_var($filters['estado'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['vlan'])) {
            $query->where('vlan_id', $filters['vlan']);
        }

        if (!empty($filters['ssid'])) {
            $query->where('ssid_wifi', 'like', '%' . $filters['ssid'] . '%');
        }

        if (isset($filters['dhcp']) && $filters['dhcp'] !== '') {
            $query->where('dhcp_activado', filter_var($filters['dhcp'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }

    public function create(array $data): RedConfig
    {
        $data['tenant_id'] = auth()->user()->business_instance_id;

        return RedConfig::create($data);
    }

    public function update(RedConfig $redConfig, array $data): RedConfig
    {
        $redConfig->update($data);
        return $redConfig;
    }

    public function delete(RedConfig $redConfig): void
    {
        $redConfig->delete();
    }

    public function toggleActiva(RedConfig $redConfig): RedConfig
    {
        $redConfig->update(['activo' => !$redConfig->activo]);
        return $redConfig->fresh();
    }
}
