@extends('layouts.app')

@section('title', 'Revisión por Dirección')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_ejecucion { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-programada_type { background: #dbeafe; color: #2563eb; }
    .badge-extraordinaria { background: #fee2e2; color: #dc2626; }
    .entrada-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: .75rem; }
    .salida-item { border-left: 3px solid #f59e0b; padding-left: 1rem; margin-bottom: .75rem; }
</style>
@endpush

@section('content')
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Revisión #{{ $revision->numero ?? '-' }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.revision-direccion.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        {{ $revision->fecha ? $revision->fecha->format('d/m/Y') : '' }} — {{ $revision->tipo_label }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $revision->estado }} fs-6">{{ $revision->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            {{-- Información General --}}
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Revisión</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Nº</div>
                            <div class="detail-value"><code>{{ $revision->numero ? '#' . $revision->numero : '-' }}</code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha</div>
                            <div class="detail-value">{{ $revision->fecha ? $revision->fecha->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Tipo</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $revision->tipo }}">{{ $revision->tipo_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Duración</div>
                            <div class="detail-value">{{ $revision->duracion_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $revision->estado }}">{{ $revision->estado_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Creado Por</div>
                            <div class="detail-value">{{ $revision->creador_label }}</div>
                        </div>
                        @if($revision->resumen)
                        <div class="col-12">
                            <div class="detail-label">Resumen</div>
                            <div class="detail-value">{{ $revision->resumen }}</div>
                        </div>
                        @endif
                        @if($revision->resumen_resoluciones)
                        <div class="col-12">
                            <div class="detail-label">Resumen de Resoluciones</div>
                            <div class="detail-value">{{ $revision->resumen_resoluciones }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Asistentes --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Asistentes ({{ $revision->asistentes_count }})</h6>
                    @if($revision->asistentes->count())
                        <div class="table-responsive">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Asistió</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($revision->asistentes as $asist)
                                <tr>
                                    <td>{{ $asist->nombre ?? $asist->usuario?->name ?? '-' }}</td>
                                    <td>{{ $asist->cargo ?? '-' }}</td>
                                    <td>
                                        @if($asist->asistio)
                                            <i class="bi bi-check-circle-fill text-success"></i> Sí
                                        @else
                                            <i class="bi bi-x-circle-fill text-danger"></i> No
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        <p class="text-muted small mb-0">No hay asistentes registrados.</p>
                    @endif
                </div>
            </div>

            {{-- Entradas --}}
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#3b82f6;--accent-hover:#2563eb;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-down-left me-2"></i>Entradas ({{ $revision->entradas->count() }})</h6>
                    @if($revision->entradas->count())
                        @foreach($revision->entradas as $entrada)
                        <div class="entrada-item">
                            <div class="fw-bold small">{{ $entrada->titulo ?? 'Entrada #' . $entrada->id }}</div>
                            @if(isset($entrada->descripcion))
                            <div class="text-muted small">{{ $entrada->descripcion }}</div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay entradas registradas.</p>
                    @endif
                </div>
            </div>

            {{-- Salidas --}}
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-up-right me-2"></i>Salidas / Acuerdos ({{ $revision->salidas_count }})</h6>
                    @if($revision->salidas->count())
                        @foreach($revision->salidas as $salida)
                        <div class="salida-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-bold small">{{ $salida->titulo ?? 'Salida #' . $salida->id }}</div>
                                @if(isset($salida->estado))
                                <span class="badge-status badge-{{ $salida->estado === 'pendiente' ? 'programada' : 'completada' }}">{{ $salida->estado }}</span>
                                @endif
                            </div>
                            @if(isset($salida->responsable))
                            <div class="text-muted small">Responsable: {{ $salida->responsable }}</div>
                            @endif
                            @if(isset($salida->fecha_limite))
                            <div class="text-muted small">Fecha límite: {{ $salida->fecha_limite->format('d/m/Y') }}</div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay salidas registradas.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        @if($revision->estado === 'programada')
                        <form action="{{ route('sgc.revision-direccion.completar', $revision) }}" method="POST" class="d-grid">
                            @csrf
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('sgc.revision-direccion.acta', $revision) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> Ver Acta
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Resumen</h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="detail-label">Asistentes</div>
                            <div class="detail-value fw-bold">{{ $revision->asistentes_count }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Presentes</div>
                            <div class="detail-value fw-bold text-success">{{ $revision->asistentes_presentes_count }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Entradas</div>
                            <div class="detail-value fw-bold text-primary">{{ $revision->entradas->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Salidas</div>
                            <div class="detail-value fw-bold text-warning">{{ $revision->salidas_count }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
