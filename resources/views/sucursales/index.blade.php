@extends('layouts.app')

@section('title', 'Sucursales')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-building text-primary me-2"></i>Sucursales</h2>
            <p class="text-muted mb-0">Gesti&oacute;n de sucursales o puntos de venta.</p>
        </div>
        @can('sucursales.create')
        <a href="{{ route('sucursales.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Nueva Sucursal
        </a>
        @endcan
    </div>

    {{-- Search/Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <form method="GET" action="{{ route('sucursales.index') }}" class="row g-2 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-white" placeholder="Buscar por nombre, código o teléfono..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                    <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">C&oacute;digo</th>
                        <th>Nombre</th>
                        <th>Tel&eacute;fono</th>
                        <th>RNC</th>
                        <th class="text-center">Matriz</th>
                        <th class="text-center">Activa</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sucursales as $s)
                    <tr>
                        <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">{{ $s->codigo }}</span></td>
                        <td class="fw-bold">{{ $s->nombre }}</td>
                        <td>{{ $s->telefono ?? '—' }}</td>
                        <td>{{ $s->rnc ?? '—' }}</td>
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
                            @can('sucursales.edit')
                            <a href="{{ route('sucursales.edit', $s) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('sucursales.delete')
                            <form action="{{ route('sucursales.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la sucursal {{ $s->nombre }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger rounded-pill" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay sucursales registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
