@extends('layouts.app')

@section('title', 'Tipos de Equipo')

@section('content')
<div class="container-fluid py-3">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0"><i class="bi bi-cpu me-2"></i>Tipos de Equipo</h2>
            <p class="text-muted mb-0">Gestión de tipos de equipos de climatización</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado</h5>
                <a href="{{ route('climatizacion.tipos-equipos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo Tipo
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        <option value="residencial" {{ request('categoria') === 'residencial' ? 'selected' : '' }}>Residencial</option>
                        <option value="comercial" {{ request('categoria') === 'comercial' ? 'selected' : '' }}>Comercial</option>
                        <option value="industrial" {{ request('categoria') === 'industrial' ? 'selected' : '' }}>Industrial</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="activo" class="form-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary me-2"><i class="bi bi-search me-1"></i>Filtrar</button>
                    <a href="{{ route('climatizacion.tipos-equipos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i>Limpiar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Slug</th>
                            <th>Categoría</th>
                            <th>Icono</th>
                            <th>Orden</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipos as $tipo)
                        <tr>
                            <td class="fw-medium">{{ $tipo->nombre }}</td>
                            <td><code>{{ $tipo->slug }}</code></td>
                            <td>{{ ucfirst($tipo->categoria) }}</td>
                            <td>@if($tipo->icono)<i class="bi {{ $tipo->icono }}"></i>@else-@endif</td>
                            <td>{{ $tipo->orden }}</td>
                            <td>
                                @if($tipo->activo)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('climatizacion.tipos-equipos.show', $tipo) }}" class="btn btn-outline-info" title="Ver"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('climatizacion.tipos-equipos.edit', $tipo) }}" class="btn btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('climatizacion.tipos-equipos.destroy', $tipo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este tipo de equipo?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay tipos de equipo registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $tipos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
