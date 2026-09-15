@extends('layouts.app')
@section('title', 'Exhibiciones')
@push('styles')
@include('partials.premium-ui')
@endpush
@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-easel"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-calendar-event me-1"></i>EVENTOS
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Exhibiciones</h2>
                    <p class="mb-0 opacity-75">Exhibiciones y eventos de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.exhibiciones.create') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Exhibición
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('arte.exhibiciones.index') }}">
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="ui-input" value="{{ request('q') }}" placeholder="Buscar por nombre o ubicación...">
                    <button class="ui-btn ui-btn-solid rounded-pill ms-2 px-4" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Exhibición</th>
                            <th>Ubicación</th>
                            <th>Fechas</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exhibiciones as $e)
                        <tr>
                            <td class="ps-4">{{ $e->id }}</td>
                            <td class="fw-semibold">{{ $e->nombre }}</td>
                            <td>{{ $e->ubicacion ?? '—' }}</td>
                            <td class="small">{{ $e->rango_fechas }}</td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">{{ $e->obras_count }}</span></td>
                            <td class="text-center">
                                <span class="badge {{ $e->activa ? 'bg-success' : 'bg-secondary' }} rounded-pill">{{ $e->activa ? 'Activa' : 'Inactiva' }}</span>
                            </td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="{{ route('arte.exhibiciones.show', $e) }}" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('arte.exhibiciones.edit', $e) }}" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('arte.exhibiciones.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la exhibición {{ addslashes($e->nombre) }}?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay exhibiciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">{{ $exhibiciones->links() }}</div>
    </div>
</div>
</div>
@endsection