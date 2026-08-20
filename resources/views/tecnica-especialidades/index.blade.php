@extends('layouts.app')
@section('title', 'Especialidades Técnicas')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #f59e0b;
    --dt-accent-gradient: linear-gradient(135deg, #f59e0b, #d97706);
    --dt-accent-rgb: 245,158,11;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Especialidades Técnicas</h4>
                    <div class="ui-header-meta">Gestiona las especialidades de los técnicos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnica-especialidades.create')
                <a href="{{ route('tecnica-especialidades.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Especialidad
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
        <div class="col-lg-4 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Total Especialidades</div>
                    <div class="ui-stat-value">{{ $especialidades->total() ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Activas</div>
                    <div class="ui-stat-value" style="color:#22c55e;">
                        {{ $especialidades->where('activo', true)->count() ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="ui-stat">
                <div class="ui-stat-body">
                    <div class="ui-stat-label">Técnicos Especializados</div>
                    <div class="ui-stat-value" style="color:#3b82f6;">
                        {{ $especialidades->sum('tecnicos_count') ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tecnica-especialidades.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Buscar especialidad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
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
                <table class="table table-hover dt-table" id="especialidadesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Técnicos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($especialidades as $esp)
                        <tr>
                            <td>{{ $esp->id }}</td>
                            <td><strong>{{ $esp->nombre }}</strong></td>
                            <td>{{ Str::limit($esp->descripcion, 50) ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $esp->tecnicos_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $esp->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $esp->activo_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('tecnica-especialidades.show', $esp) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('tecnica-especialidades.edit')
                                    <a href="{{ route('tecnica-especialidades.edit', $esp) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('tecnica-especialidades.delete')
                                    @if($esp->tecnicos_count == 0 || !isset($esp->tecnicos_count))
                                    <form action="{{ route('tecnica-especialidades.destroy', $esp) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta especialidad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay especialidades registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $especialidades->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#especialidadesTable').DataTable({
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
