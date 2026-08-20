@extends('layouts.app')
@section('title', 'Marcas Tecnológicas')

@push('styles')
@include('partials.premium-ui')
@include('partials.datatable-ui')
<style>
:root {
    --dt-accent: #3b82f6;
    --dt-accent-gradient: linear-gradient(135deg, #3b82f6, #2563eb);
    --dt-accent-rgb: 59,130,246;
}
.marca-logo {
    width: 40px;
    height: 40px;
    border-radius: 0.5rem;
    object-fit: cover;
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Marcas Tecnológicas</h4>
                    <div class="ui-header-meta">Administra las marcas de productos tecnológicos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('marca-tecnologicas.create')
                <a href="{{ route('marcas-tecnologicas.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Marca
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

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('marcas-tecnologicas.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar marca..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="activo" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="pais" class="form-control" placeholder="País..." value="{{ request('pais') }}">
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
                <table class="table table-hover dt-table" id="marcasTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Website</th>
                            <th>País</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($marcas as $marca)
                        <tr>
                            <td>{{ $marca->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($marca->logo_url)
                                    <img src="{{ $marca->logo_url }}" alt="{{ $marca->nombre }}" class="marca-logo me-2" onerror="this.style.display='none'">
                                    @endif
                                    <strong>{{ $marca->nombre }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($marca->website)
                                <a href="{{ $marca->website }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-globe me-1"></i>{{ $marca->website }}
                                </a>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $marca->pais ?? '-' }}</td>
                            <td>
                                <span class="badge bg-info">{{ $marca->productos_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $marca->activo ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $marca->activo_label }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('marcas-tecnologicas.show', $marca) }}" class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('marca-tecnologicas.edit')
                                    <a href="{{ route('marcas-tecnologicas.edit', $marca) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan

                                    @if($marca->productos_count == 0 || !isset($marca->productos_count))
                                    @can('marca-tecnologicas.delete')
                                    <form action="{{ route('marcas-tecnologicas.destroy', $marca) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta marca?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                                No hay marcas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $marcas->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#marcasTable').DataTable({
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
