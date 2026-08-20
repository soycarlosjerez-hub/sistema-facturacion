@extends('layouts.app')
@section('title', 'Redes de Configuración')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #06b6d4;
    --dt-accent-gradient: linear-gradient(135deg, #06b6d4, #0891b2);
    --dt-accent-rgb: 6,182,212;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Redes de Configuración</h4>
                    <div class="ui-header-meta">Administra infraestructura de red para clientes empresariales</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('redes-config.create')
                <a href="{{ route('redes-config.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Red
                </a>
                @endcan
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Redes</div>
                    <div class="ui-stat-value">{{ $redes->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activas</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $redes->where('activo', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Con DHCP</div>
                    <div class="ui-stat-value" style="color:#3b82f6;">
                        {{ $redes->where('dhcp_activado', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Con VLAN</div>
                    <div class="ui-stat-value" style="color:#8b5cf6;">
                        {{ $redes->whereNotNull('vlan_id')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('redes-config.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar red/SSID..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="vlan_id" class="form-select">
                        <option value="">Todas las VLANs</option>
                        @foreach($redes->pluck('vlan_id')->filter()->unique()->sort() as $vlan)
                        <option value="{{ $vlan }}" {{ request('vlan_id') == $vlan ? 'selected' : '' }}>
                            VLAN {{ $vlan }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover dt-table" id="redesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre de Red</th>
                            <th>SSID WiFi</th>
                            <th>VLAN</th>
                            <th>Cliente</th>
                            <th>DHCP</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redes as $red)
                        <tr>
                            <td>{{ $red->id }}</td>
                            <td><strong>{{ $red->nombre_red }}</strong></td>
                            <td>{{ $red->ssid_wifi ?? '-' }}</td>
                            <td>
                                @if($red->vlan_id)
                                <span class="badge bg-info">VLAN {{ $red->vlan_id }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $red->cliente->nombre ?? '-' }}</td>
                            <td>
                                @if($red->dhcp_activado)
                                <span class="badge bg-success">Activo</span>
                                @else
                                <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $red->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $red->activo_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('redes-config.show', $red) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('redes-config.edit')
                                    <a href="{{ route('redes-config.edit', $red) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('redes-config.delete')
                                    <form action="{{ route('redes-config.destroy', $red) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta configuración?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay configuraciones de red registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $redes->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#redesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "order": [[0, "desc"]],
        "pageLength": 10,
        "responsive": true
    });
});
</script>
@endpush
@endsection
