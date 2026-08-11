@extends('layouts.app')
@section('title', 'Exhibiciones')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-easel"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Exhibiciones</h2>
                    <p class="text-white text-opacity-75 mb-0">Exhibiciones y eventos de la galería</p>
                </div>
            </div>
            <a href="{{ route('arte.exhibiciones.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nueva Exhibición
            </a>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <form method="GET" action="{{ route('arte.exhibiciones.index') }}">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search"></i></span>
                <input type="text" name="q" class="form-control rounded-3" value="{{ request('q') }}" placeholder="Buscar por nombre o ubicación...">
                <button class="btn btn-primary rounded-pill ms-2 px-4" type="submit">Buscar</button>
            </div>
        </form>
    </div>

    <div class="premium-card">
        <div class="card-accent purple"></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>#</th>
                        <th>Exhibición</th>
                        <th>Ubicación</th>
                        <th>Fechas</th>
                        <th class="text-center">Obras</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exhibiciones as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td class="fw-medium">{{ $e->nombre }}</td>
                        <td>{{ $e->ubicacion ?? '—' }}</td>
                        <td class="small">{{ $e->rango_fechas }}</td>
                        <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $e->obras_count }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $e->activa ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $e->activa ? 'Activa' : 'Inactiva' }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('arte.exhibiciones.show', $e) }}" class="premium-btn-edit" title="Ver"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('arte.exhibiciones.edit', $e) }}" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('arte.exhibiciones.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la exhibición {{ addslashes($e->nombre) }}?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay exhibiciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $exhibiciones->links() }}</div>
    </div>
</div>
@endsection
