@extends('layouts.app')
@section('title', 'Consignaciones')
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
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-arrow-left-right me-1"></i>TERCEROS
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Consignaciones</h2>
                    <p class="mb-0 opacity-75">Obras en consignación de terceros</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('arte.consignaciones.create') }}" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Consignación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="{{ route('arte.consignaciones.index') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="ui-label" for="q">Buscar</label>
                    <input type="text" name="q" id="q" class="ui-input" value="{{ request('q') }}" placeholder="Consignante u obra...">
                </div>
                <div class="col-md-3">
                    <label class="ui-label" for="estado">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="">Todos</option>
                        @foreach(['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada'] as $k => $v)
                            <option value="{{ $k }}" {{ request('estado') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="ui-btn ui-btn-solid rounded-pill w-100" type="submit" title="Filtrar"><i class="bi bi-search"></i></button>
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
                            <th>Obra</th>
                            <th>Consignante</th>
                            <th class="text-end">Comisión</th>
                            <th>Inicio</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consignaciones as $c)
                        <tr>
                            <td class="ps-4">{{ $c->id }}</td>
                            <td class="fw-semibold">{{ $c->obra?->titulo ?? '—' }}</td>
                            <td>{{ $c->consignante }}</td>
                            <td class="text-end">{{ $c->porcentaje_comision }}%</td>
                            <td class="small">{{ optional($c->fecha_inicio)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-{{ $c->estado_badge_class }} rounded-pill">{{ $c->estado_label }}</span></td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="{{ route('arte.consignaciones.edit', $c) }}" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('arte.consignaciones.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta consignación?')">
                                    @csrf @method('DELETE')
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay consignaciones registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3">{{ $consignaciones->links() }}</div>
    </div>
</div>
</div>
@endsection