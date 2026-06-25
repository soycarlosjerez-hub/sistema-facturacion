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
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Gestión de Sucursales</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-geo-alt me-1"></i>
                        Administra las sucursales, ubicaciones y datos de contacto
                    </small>
                </div>
            </div>
            <div>
                @can('sucursales.create')
                <a href="{{ route('sucursales.create') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Sucursal
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="premium-card mb-4" style="animation-delay:.1s;">
        <div class="card-accent purple"></div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('sucursales.index') }}" class="row g-2 align-items-end">
                <div class="col-lg-5">
                    <label class="form-label small fw-bold text-muted">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre, dirección o teléfono..." value="{{ request('buscar') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                    <a href="{{ route('sucursales.index') }}" class="btn btn-outline-secondary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="premium-card" style="animation-delay:.2s;">
        <div class="card-accent purple"></div>
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
                        <td class="ps-4"><span class="premium-badge">{{ $s->codigo }}</span></td>
                        <td class="fw-bold">
                            <a href="{{ route('sucursales.show', $s) }}" class="text-decoration-none">{{ $s->nombre }}</a>
                        </td>
                        <td class="text-center"><span class="premium-badge">{{ $s->almacenes_count }}</span></td>
                        <td class="text-center"><span class="premium-badge">{{ $s->cajas_count }}</span></td>
                        <td>{{ $s->telefono ?? '—' }}</td>
                        <td class="text-center">
                            @if($s->es_matriz)
                                <span class="premium-badge active"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($s->activa)
                                <span class="premium-badge active"><i class="bi bi-check-circle me-1"></i>Activa</span>
                            @else
                                <span class="premium-badge"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('sucursales.show', $s) }}" class="btn btn-icon-hover text-info border-0 bg-transparent" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('sucursales.edit')
                            <a href="{{ route('sucursales.edit', $s) }}" class="premium-btn-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('sucursales.delete')
                            <form action="{{ route('sucursales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la sucursal {{ $s->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete" title="Eliminar">
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
