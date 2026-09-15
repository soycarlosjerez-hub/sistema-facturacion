@extends('layouts.app')

@section('title', 'No Conformidad')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-abierta { background: #fee2e2; color: #dc2626; }
    .badge-en_analisis { background: #fef3c7; color: #d97706; }
    .badge-en_accion { background: #dbeafe; color: #2563eb; }
    .badge-verificando { background: #e0f2fe; color: #0284c7; }
    .badge-cerrada { background: #dcfce7; color: #16a34a; }
    .badge-grave { background: #fee2e2; color: #dc2626; }
    .badge-menor { background: #fef3c7; color: #d97706; }
    .timeline-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: 1rem; }
    .timeline-item:last-child { border-left-color: #a5b4fc; }
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
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $noConformidad->numero_label }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.no-conformidades.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Detalle de la no conformidad
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $noConformidad->estado }} fs-6">{{ $noConformidad->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Main Info --}}
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información General</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Fecha Ocurrencia</div>
                            <div class="detail-value">{{ $noConformidad->fecha_ocurrencia ? $noConformidad->fecha_ocurrencia->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Fecha Identificación</div>
                            <div class="detail-value">{{ $noConformidad->fecha_identificacion ? $noConformidad->fecha_identificacion->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Origen</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $noConformidad->origen }}">{{ $noConformidad->origen_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Gravedad</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $noConformidad->gravedad === 'mayor' ? 'grave' : 'menor' }}">{{ $noConformidad->gravedad_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $noConformidad->estado }}">{{ $noConformidad->estado_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Asignado A</div>
                            <div class="detail-value">{{ $noConformidad->asignado_a ? $noConformidad->asignadoA->name : 'Sin asignar' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Límite</div>
                            <div class="detail-value">
                                @if($noConformidad->fecha_limite)
                                    <span class="{{ $noConformidad->es_vencida ? 'text-danger fw-bold' : '' }}">{{ $noConformidad->fecha_limite->format('d/m/Y') }}</span>
                                    @if($noConformidad->es_vencida)
                                        <small class="text-danger d-block">Vencida</small>
                                    @endif
                                @else
                                    Sin límite
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Auditoría</div>
                            <div class="detail-value">{{ $noConformidad->auditoria ? $noConformidad->auditoria->codigo : '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $noConformidad->descripcion }}</div>
                        </div>
                        @if($noConformidad->evidencia)
                        <div class="col-12">
                            <div class="detail-label">Evidencia</div>
                            <div class="detail-value">{{ $noConformidad->evidencia }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Análisis de Causa --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i>Análisis de Causa</h6>
                    @if($noConformidad->analisis_causa_metodo)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="detail-label">Método</div>
                                <div class="detail-value">{{ $noConformidad->analisis_causa_metodo_label }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-label">Acción Contención</div>
                                <div class="detail-value">{{ $noConformidad->accion_contencion ?? 'No definida' }}</div>
                            </div>
                            @if($noConformidad->analisis_causa_detalle)
                            <div class="col-12">
                                <div class="detail-label">Detalle del Análisis</div>
                                <div class="detail-value">{{ $noConformidad->analisis_causa_detalle }}</div>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-muted small mb-0">Análisis de causa no iniciado.</p>
                    @endif
                </div>
            </div>

            {{-- Acciones Correctivas --}}
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-check2-square me-2"></i>Acciones Correctivas ({{ $noConformidad->acciones_correctivas_count }})</h6>
                    @if($noConformidad->accionesCorrectivas->count())
                        @foreach($noConformidad->accionesCorrectivas as $accion)
                        <div class="timeline-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold small">{{ $accion->descripcion ?? 'Acción #' . $accion->id }}</span>
                                    @if(isset($accion->estado))
                                    <span class="badge-status badge-{{ $accion->estado === 'completada' ? 'cerrada' : 'en_accion' }} ms-1">{{ $accion->estado }}</span>
                                    @endif
                                </div>
                                @if(isset($accion->fecha_limite))
                                <small class="text-muted">{{ $accion->fecha_limite->format('d/m/Y') }}</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay acciones correctivas registradas.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Actions --}}
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        @if($noConformidad->estado !== 'cerrada')
                        <a href="{{ route('sgc.no-conformidades.edit', $noConformidad) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        @endif
                        @if($noConformidad->estado === 'abierta')
                        <form action="{{ route('sgc.no-conformidades.store') }}" method="POST" class="d-grid">
                            @csrf
                        </form>
                        @endif
                        @if(in_array($noConformidad->estado, ['verificando', 'en_accion']))
                        <form action="{{ route('sgc.no-conformidades.cerrar', $noConformidad) }}" method="POST" class="d-grid" onsubmit="return confirm('¿Cerrar esta no conformidad?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Cerrar NC
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Audit Info --}}
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $noConformidad->creador ? $noConformidad->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $noConformidad->created_at ? $noConformidad->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $noConformidad->updated_at ? $noConformidad->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
