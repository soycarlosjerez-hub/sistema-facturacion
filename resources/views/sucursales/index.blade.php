@extends('layouts.app')

@section('title', 'Sucursales')

@push('styles')
@include('partials.premium-ui')
<style>
.sucursales-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(139,92,246,.04);
    margin: 0;
}
.sucursales-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.sucursales-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.sucursales-table tbody tr:last-child td { border-bottom: none; }
.sucursales-table tbody tr { transition: background .15s; }
.sucursales-table tbody tr:hover { background: rgba(139,92,246,.03); }

body.dark-mode .sucursales-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .sucursales-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <h4 class="ui-header-title">Gestión de Sucursales</h4>
                    <div class="ui-header-meta"><i class="bi bi-geo-alt me-1"></i> <span>Administra las sucursales, ubicaciones y datos de contacto</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('sucursales.create')
                <a href="{{ route('sucursales.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-plus-lg me-1"></i> Nueva Sucursal</a>
                @endcan
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sucursales.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-5">
                    <label class="ui-label">Buscar</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="ui-input" placeholder="Nombre, dirección o teléfono..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                    <a href="{{ route('sucursales.index') }}" class="ui-btn ui-btn-ghost rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="table sucursales-table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">C&oacute;digo</th>
                        <th>Nombre</th>
                        <th class="text-center">Almacenes</th>
                        <th class="text-center">Cajas</th>
                        <th>Tel&eacute;fono</th>
                        <th class="text-center">Matriz</th>
                        <th class="text-center">Activa</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sucursales as $s)
                    <tr>
                        <td class="ps-4"><span class="ui-badge ui-badge-neutral">{{ $s->codigo }}</span></td>
                        <td class="fw-bold">
                            <a href="{{ route('sucursales.show', $s) }}" class="text-decoration-none">{{ $s->nombre }}</a>
                        </td>
                        <td class="text-center"><span class="ui-badge ui-badge-neutral">{{ $s->almacenes_count }}</span></td>
                        <td class="text-center"><span class="ui-badge ui-badge-neutral">{{ $s->cajas_count }}</span></td>
                        <td>{{ $s->telefono ?? '—' }}</td>
                        <td class="text-center">
                            @if($s->es_matriz)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($s->activa)
                                <span class="ui-badge ui-badge-success"><i class="bi bi-check-circle me-1"></i>Activa</span>
                            @else
                                <span class="ui-badge ui-badge-neutral"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('sucursales.show', $s) }}" class="btn btn-icon-hover text-info border-0 bg-transparent" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('sucursales.edit')
                            <a href="{{ route('sucursales.edit', $s) }}" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('sucursales.delete')
                            <form action="{{ route('sucursales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar la sucursal {{ $s->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="ui-action ui-action-delete" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay sucursales registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sucursales->hasPages())
        <div class="border-0 py-3 px-4" style="background:transparent;">
            {{ $sucursales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
