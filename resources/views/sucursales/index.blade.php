@extends('layouts.app')

@section('title', 'Sucursales')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .status-badge {
        padding: 0.4em 0.8em; border-radius: 2rem;
        font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 d-flex align-items-center">
                <i class="bi bi-geo-alt me-3 fs-1 opacity-75"></i>Gestión de Sucursales
            </h2>
            <p class="mb-0 opacity-75 fs-5">Administra las sucursales, ubicaciones y datos de contacto</p>
        </div>
        <div>
            @can('sucursales.create')
            <a href="{{ route('sucursales.create') }}" class="btn btn-light text-primary fw-bold rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Nueva Sucursal
            </a>
            @endcan
        </div>
    </div>

    <div class="filter-card p-3 mb-4">
        <form method="GET" action="{{ route('sucursales.index') }}" class="row g-2 align-items-end">
            <div class="col-lg-5">
                <label class="form-label text-muted small fw-bold text-uppercase tracking-wider mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0 ps-0" placeholder="Nombre, dirección o teléfono..." value="{{ request('buscar') }}" autocomplete="off">
                </div>
            </div>
            <div class="col-lg-3 d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-2"></i>Filtrar</button>
                <a href="{{ route('sucursales.index') }}" class="btn btn-outline-secondary rounded-circle" style="width:38px;height:38px;display:flex;align-items:center;justify-content:center;" title="Limpiar"><i class="bi bi-arrow-counterclockwise"></i></a>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                        <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ $s->codigo }}</span></td>
                        <td class="fw-bold">
                            <a href="{{ route('sucursales.show', $s) }}" class="text-decoration-none">{{ $s->nombre }}</a>
                        </td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $s->almacenes_count }}</span></td>
                        <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $s->cajas_count }}</span></td>
                        <td>{{ $s->telefono ?? '—' }}</td>
                        <td class="text-center">
                            @if($s->es_matriz)
                                <span class="badge bg-primary rounded-pill px-3"><i class="bi bi-star-fill me-1"></i>Matriz</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($s->activa)
                                <span class="badge bg-success rounded-pill px-3"><i class="bi bi-check-circle me-1"></i>Activa</span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3"><i class="bi bi-x-circle me-1"></i>Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('sucursales.show', $s) }}" class="btn btn-icon-hover text-info border-0 bg-transparent" title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </a>
                            @can('sucursales.edit')
                            <a href="{{ route('sucursales.edit', $s) }}" class="btn btn-icon-hover text-primary border-0 bg-transparent" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('sucursales.delete')
                            <form action="{{ route('sucursales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la sucursal {{ $s->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-icon-hover text-danger border-0 bg-transparent" title="Eliminar">
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
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            {{ $sucursales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
