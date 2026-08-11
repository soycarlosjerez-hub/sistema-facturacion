@extends('layouts.app')
@section('title', 'Consignaciones')
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
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Consignaciones</h2>
                    <p class="text-white text-opacity-75 mb-0">Obras en consignación de terceros</p>
                </div>
            </div>
            <a href="{{ route('arte.consignaciones.create') }}" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Nueva Consignación
            </a>
        </div>
    </div>

    <div class="premium-card mb-3">
        <div class="card-accent purple"></div>
        <form method="GET" action="{{ route('arte.consignaciones.index') }}" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label small fw-bold">Buscar</label>
                <input type="text" name="q" class="form-control rounded-3" value="{{ request('q') }}" placeholder="Consignante u obra...">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Estado</label>
                <select name="estado" class="form-select rounded-3">
                    <option value="">Todos</option>
                    @foreach(['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                        <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-primary rounded-pill w-100" type="submit"><i class="bi bi-search"></i></button>
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
                        <th>Obra</th>
                        <th>Consignante</th>
                        <th class="text-end">Comisión</th>
                        <th>Inicio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consignaciones as $c)
                    <tr>
                        <td>{{ $c->id }}</td>
                        <td class="fw-medium">{{ $c->obra?->titulo ?? '—' }}</td>
                        <td>{{ $c->consignante }}</td>
                        <td class="text-end">{{ $c->porcentaje_comision }}%</td>
                        <td class="small">{{ optional($c->fecha_inicio)->format('d/m/Y') }}</td>
                        <td><span class="badge bg-{{ $c->estado_badge_class }} rounded-pill">{{ $c->estado_label }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('arte.consignaciones.edit', $c) }}" class="premium-btn-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('arte.consignaciones.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta consignación?')">
                                @csrf @method('DELETE')
                                <button class="premium-btn-delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No hay consignaciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $consignaciones->links() }}</div>
    </div>
</div>
@endsection
