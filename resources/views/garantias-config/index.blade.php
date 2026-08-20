@extends('layouts.app')
@section('title', 'Configuración de Garantías')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #22c55e;
    --dt-accent-gradient: linear-gradient(135deg, #22c55e, #16a34a);
    --dt-accent-rgb: 34,197,94;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Configuración de Garantías</h4>
                    <div class="ui-header-meta">Define los tipos y duraciones de garantía por producto</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('garantias-config.create')
                <a href="{{ route('garantias-config.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Configuración
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
                    <div class="ui-stat-label">Total Configuraciones</div>
                    <div class="ui-stat-value">{{ $garantias->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Fabrica</div>
                    <div class="ui-stat-value" style="color:#3b82f6;">
                        {{ $garantias->where('tipo_garantia', 'fabrica')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Extendida</div>
                    <div class="ui-stat-value" style="color:#8b5cf6;">
                        {{ $garantias->where('tipo_garantia', 'extendida')->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activas</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $garantias->where('activo', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('garantias-config.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar configuración..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="tipo_garantia" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="fabrica" {{ request('tipo_garantia') == 'fabrica' ? 'selected' : '' }}>Garantía de Fábrica</option>
                        <option value="extendida" {{ request('tipo_garantia') == 'extendida' ? 'selected' : '' }}>Garantía Extendida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivas</option>
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
                <table class="table table-hover dt-table" id="garantiasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo Producto</th>
                            <th>Días</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($garantias as $garantia)
                        <tr>
                            <td>{{ $garantia->id }}</td>
                            <td><strong>{{ $garantia->nombre }}</strong></td>
                            <td>{{ $garantia->tipo_producto ?? 'General' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $garantia->dias_garantia }} días</span>
                            </td>
                            <td>
                                <span class="badge {{ $garantia->tipo_garantia == 'fabrica' ? 'bg-primary' : 'bg-warning text-dark' }}">
                                    {{ $garantia->tipo_garantia_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $garantia->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $garantia->activo_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('garantias-config.show', $garantia) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('garantias-config.edit')
                                    <a href="{{ route('garantias-config.edit', $garantia) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('garantias-config.delete')
                                    <form action="{{ route('garantias-config.destroy', $garantia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta configuración?')">
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
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay configuraciones de garantía registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $garantias->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#garantiasTable').DataTable({
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
