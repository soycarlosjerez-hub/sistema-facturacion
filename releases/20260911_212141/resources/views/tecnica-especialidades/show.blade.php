@extends('layouts.app')
@section('title', 'Ver Especialidad Técnica')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $tecnicaEspecialidad->nombre }}</h4>
                    <div class="ui-header-meta">Detalles de la especialidad técnica</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('tecnica-especialidades.edit')
                <a href="{{ route('tecnica-especialidades.edit', $tecnicaEspecialidad) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('tecnica-especialidades.index') }}" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información General</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong>{{ $tecnicaEspecialidad->nombre }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Descripción</small>
                        <p class="text-muted">{{ $tecnicaEspecialidad->descripcion ?? 'Sin descripción' }}</p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden de Visualización</small>
                        <strong>{{ $tecnicaEspecialidad->orden }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge {{ $tecnicaEspecialidad->activo ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $tecnicaEspecialidad->activo_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Técnicos Asignados</small>
                        <span class="badge bg-info fs-6">{{ $tecnicaEspecialidad->tecnicos_count ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Técnicos con esta Especialidad</h6>
                </div>
                <div class="card-body">
                    @if($tecnicaEspecialidad->tecnicos && $tecnicaEspecialidad->tecnicos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Técnico</th>
                                    <th>Especialidad</th>
                                    <th>Teléfono</th>
                                    <th>Órdenes</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tecnicaEspecialidad->tecnicos as $tecnico)
                                <tr>
                                    <td>
                                        <a href="{{ route('tecnicos.show', $tecnico) }}" class="text-decoration-none">
                                            {{ $tecnico->nombre }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($tecnico->pivot && $tecnico->pivot->activo)
                                        <span class="badge bg-success">Activa</span>
                                        @else
                                        <span class="badge bg-secondary">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>{{ $tecnico->telefono ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $tecnico->ordenes_reparacion_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $tecnico->activo ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $tecnico->activo_label }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                        No hay técnicos asignados a esta especialidad
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
