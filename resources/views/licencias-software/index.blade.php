@extends('layouts.app')
@section('title', 'Licencias de Software')

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
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Licencias de Software</h4>
                    <div class="ui-header-meta">Administra claves de licencia y sus estados</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('licencias-software.create')
                <a href="{{ route('licencias-software.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Licencia
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
                    <div class="ui-stat-label">Total Licencias</div>
                    <div class="ui-stat-value">{{ $licencias->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activas</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $licuencias->where('licencia_activa', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Por Vencer</div>
                    <div class="ui-stat-value" style="color:#f59e0b;">
                        {{ $licencias->where('fecha_vencimiento', '<=', now()->addDays(30))->where('licencia_activa', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Vencidas</div>
                    <div class="ui-stat-value" style="color:#ef4444;">
                        {{ $licencias->where('fecha_vencimiento', '<', now())->where('licencia_activa', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('licencias-software.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar clave/licencia..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="plataforma" class="form-select">
                        <option value="">Todas las plataformas</option>
                        <option value="Windows" {{ request('plataforma') == 'Windows' ? 'selected' : '' }}>Windows</option>
                        <option value="macOS" {{ request('plataforma') == 'macOS' ? 'selected' : '' }}>macOS</option>
                        <option value="Linux" {{ request('plataforma') == 'Linux' ? 'selected' : '' }}>Linux</option>
                        <option value="Cloud" {{ request('plataforma') == 'Cloud' ? 'selected' : '' }}>Cloud</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="tipo_licencia" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="perpetua" {{ request('tipo_licencia') == 'perpetua' ? 'selected' : '' }}>Perpetua</option>
                        <option value="suscripcion" {{ request('tipo_licencia') == 'suscripcion' ? 'selected' : '' }}>Suscripción</option>
                        <option value="open_source" {{ request('tipo_licencia') == 'open_source' ? 'selected' : '' }}>Open Source</option>
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
                <table class="table table-hover dt-table" id="licenciasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Clave de Licencia</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Plataforma</th>
                            <th>Usuario</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($licencias as $licencia)
                        <tr>
                            <td>{{ $licencia->id }}</td>
                            <td><code>{{ $licencia->clave_licencia }}</code></td>
                            <td>{{ $licencia->producto->nombre ?? '-' }}</td>
                            <td>{{ $licencia->tipo_licencia ?? '-' }}</td>
                            <td><span class="badge bg-secondary">{{ $licencia->plataforma ?? '-' }}</span></td>
                            <td>{{ $licencia->usuario_asignado ?? '-' }}</td>
                            <td>{{ $licencia->fecha_vencimiento ? $licencia->fecha_vencimiento->format('Y-m-d') : '-' }}</td>
                            <td>
                                @php
                                    $estado = 'Activa';
                                    $badgeClass = 'success';
                                    
                                    if (!$licencia->licencia_activa) {
                                        $estado = 'Inactiva';
                                        $badgeClass = 'secondary';
                                    } elseif ($licencia->fecha_vencimiento && $licencia->fecha_vencimiento->lt(now())) {
                                        $estado = 'Vencida';
                                        $badgeClass = 'danger';
                                    } elseif ($licencia->fecha_vencimiento && $licencia->fecha_vencimiento->lte(now()->addDays(30))) {
                                        $estado = 'Por Vencer';
                                        $badgeClass = 'warning';
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $estado }}</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('licencias-software.show', $licencia) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('licencias-software.edit')
                                    <a href="{{ route('licencias-software.edit', $licencia) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('licencias-software.delete')
                                    <form action="{{ route('licencias-software.destroy', $licencia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta licencia?')">
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
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay licencias registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $licencias->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#licenciasTable').DataTable({
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
