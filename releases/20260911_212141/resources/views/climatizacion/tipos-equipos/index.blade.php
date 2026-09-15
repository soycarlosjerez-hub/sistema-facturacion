@extends('layouts.app')

@section('title', 'Tipos de Equipo')

@push('styles')
@include('partials.premium-ui')
<style>
/* Estilos específicos para tipos de equipo climatización */
.ui-header .ui-avatar-circle {
    background: rgba(6,182,212,.25);
    border-color: rgba(6,182,212,.4);
}

body.dark-mode .ui-header .ui-avatar-circle {
    background: rgba(6,182,212,.2);
    border-color: rgba(6,182,212,.35);
}
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    {{-- ============================================================
        HEADER PREMIUM
    ============================================================ --}}
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Tipos de Equipo</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>
                        Gestión de tipos de equipos de climatización
                        <span class="divider">·</span>
                        <i class="bi bi-list-ul me-1"></i>
                        <span>{{ $tipos->total() }} registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('climatizacion.tipos-equipos.create')
                <a href="{{ route('climatizacion.tipos-equipos.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Tipo
                </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- ============================================================
        FILTROS
    ============================================================ --}}
    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('climatizacion.tipos-equipos.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-4">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Buscar por nombre o slug..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="categoria" class="ui-select">
                        <option value="">Todas las categorías</option>
                        <option value="residencial" {{ request('categoria') === 'residencial' ? 'selected' : '' }}>Residencial</option>
                        <option value="comercial" {{ request('categoria') === 'comercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="industrial" {{ request('categoria') === 'industrial' ? 'selected' : '' }}>Industrial</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="activo" class="ui-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="ui-btn ui-btn-solid flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="ui-btn ui-btn-ghost" title="Limpiar filtros"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================
        TABLA DE DATOS
    ============================================================ --}}
    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Slug</th>
                            <th>Categoría</th>
                            <th>Icono</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tipos as $tipo)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-semibold">{{ $tipo->nombre }}</span>
                                </td>
                                <td>
                                    <code class="small">{{ $tipo->slug }}</code>
                                </td>
                                <td>
                                    @php
                                        $catColors = [
                                            'residencial' => 'ui-badge-success',
                                            'comercial'   => 'ui-badge-info',
                                            'industrial'  => 'ui-badge-warning',
                                        ];
                                        $catIcons = [
                                            'residencial' => 'bi-house-door',
                                            'comercial'   => 'bi-building',
                                            'industrial'  => 'bi-gear',
                                        ];
                                        $catClass = $catColors[$tipo->categoria] ?? 'ui-badge-neutral';
                                        $catIcon  = $catIcons[$tipo->categoria] ?? 'bi-tag';
                                    @endphp
                                    <span class="ui-badge {{ $catClass }}">
                                        <i class="bi {{ $catIcon }} me-1"></i>{{ ucfirst($tipo->categoria) }}
                                    </span>
                                </td>
                                <td>
                                    @if($tipo->icono)
                                        <i class="bi {{ $tipo->icono }} fs-5"></i>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">{{ $tipo->orden }}</span>
                                </td>
                                <td>
                                    @if($tipo->activo)
                                        <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activo</span>
                                    @else
                                        <span class="ui-badge ui-badge-danger"><i class="bi bi-x-circle me-1"></i>Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('climatizacion.tipos-equipos.show', $tipo) }}"
                                           class="ui-action ui-action-view" title="Ver detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @can('climatizacion.tipos-equipos.edit')
                                        <a href="{{ route('climatizacion.tipos-equipos.edit', $tipo) }}"
                                           class="ui-action ui-action-edit" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @endcan
                                        @can('climatizacion.tipos-equipos.destroy')
                                        <button type="button" class="ui-action ui-action-delete"
                                                onclick="UI.confirm.delete('{{ route('climatizacion.tipos-equipos.destroy', $tipo) }}', '{{ addslashes($tipo->nombre) }}')"
                                                title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="ui-empty-state">
                                        <i class="bi bi-cpu"></i>
                                        <p>No hay tipos de equipo registrados</p>
                                        @can('climatizacion.tipos-equipos.create')
                                        <a href="{{ route('climatizacion.tipos-equipos.create') }}" class="ui-btn ui-btn-solid ui-btn-sm mt-2 rounded-pill">
                                            <i class="bi bi-plus-lg me-1"></i>Crear primer tipo
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================
        PAGINACIÓN
    ============================================================ --}}
    @if($tipos->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $tipos->links() }}
    </div>
    @endif

</div>
@endsection

@push('scripts')
@endpush
