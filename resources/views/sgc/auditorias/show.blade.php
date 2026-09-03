@extends('layouts.app')

@section('title', 'Auditoría Interna')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-cancelada { background: #f1f5f9; color: #64748b; }
    .checklist-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: .75rem; }
    .checklist-item:last-child { border-left-color: #a5b4fc; }
    .hallazgo-card { border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; }
    .hallazgo-card:last-child { margin-bottom: 0; }
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
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $auditoria->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.auditorias.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Auditoría — {{ $auditoria->area_auditar }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $auditoria->estado }} fs-6">{{ $auditoria->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Auditoría</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Código</div>
                            <div class="detail-value"><code>{{ $auditoria->codigo }}</code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Área a Auditar</div>
                            <div class="detail-value">{{ $auditoria->area_auditar }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $auditoria->estado }}">{{ $auditoria->estado_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Programada</div>
                            <div class="detail-value">{{ $auditoria->fecha_programada ? $auditoria->fecha_programada->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Inicio Real</div>
                            <div class="detail-value">{{ $auditoria->fecha_real_inicio ? $auditoria->fecha_real_inicio->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Fin Real</div>
                            <div class="detail-value">{{ $auditoria->fecha_real_fin ? $auditoria->fecha_real_fin->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Responsable Auditor</div>
                            <div class="detail-value">{{ $auditoria->responsable_auditor_label }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Programa</div>
                            <div class="detail-value">{{ $auditoria->programa_label }}</div>
                        </div>
                        @if($auditoria->alcance)
                        <div class="col-12">
                            <div class="detail-label">Alcance</div>
                            <div class="detail-value">{{ $auditoria->alcance }}</div>
                        </div>
                        @endif
                        @if($auditoria->criterios)
                        <div class="col-12">
                            <div class="detail-label">Criterios</div>
                            <div class="detail-value">{{ $auditoria->criterios }}</div>
                        </div>
                        @endif
                        @if($auditoria->cumplimiento_general)
                        <div class="col-md-4">
                            <div class="detail-label">Cumplimiento General</div>
                            <div class="detail-value fw-bold">{{ $auditoria->cumplimiento_general_label }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i>Checklist ({{ $auditoria->checklistItems->count() }})</h6>
                    @if($auditoria->checklistItems->count())
                        @foreach($auditoria->checklistItems as $item)
                        <div class="checklist-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold small">{{ $item->descripcion ?? 'Item #' . $item->id }}</span>
                                    @if(isset($item->cumplimiento))
                                    <span class="badge-status badge-{{ $item->cumplimiento === 'conforme' ? 'completada' : 'programada' }} ms-1">{{ $item->cumplimiento }}</span>
                                    @endif
                                </div>
                            </div>
                            @if(isset($item->observacion) && $item->observacion)
                            <div class="text-muted small mt-1">{{ $item->observacion }}</div>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay items en el checklist.</p>
                    @endif
                </div>
            </div>

            {{-- Hallazgos --}}
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-circle me-2"></i>Hallazgos ({{ $auditoria->hallazgos_count }})</h6>
                    @if($auditoria->hallazgos->count())
                        @foreach($auditoria->hallazgos as $hallazgo)
                        <div class="hallazgo-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge-status badge-{{ $hallazgo->tipo ?? 'programada' }}">{{ $hallazgo->tipo ?? 'Hallazgo' }}</span>
                                <small class="text-muted">{{ $hallazgo->created_at ? $hallazgo->created_at->format('d/m/Y') : '' }}</small>
                            </div>
                            <p class="mb-1 small">{{ $hallazgo->descripcion ?? 'Sin descripción' }}</p>
                            @if(isset($hallazgo->area))
                            <small class="text-muted">Área: {{ $hallazgo->area }}</small>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay hallazgos registrados.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Actions --}}
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        @if($auditoria->estado === 'programada')
                        <form action="{{ route('sgc.auditorias.iniciar', $auditoria) }}" method="POST" class="d-grid">
                            @csrf
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-play me-1"></i> Iniciar Auditoría
                            </button>
                        </form>
                        @endif
                        @if($auditoria->estado === 'en_curso')
                        <form action="{{ route('sgc.auditorias.completar', $auditoria) }}" method="POST" class="d-grid" onsubmit="return confirm('¿Marcar auditoría como completada?')">
                            @csrf
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('sgc.auditorias.informe', $auditoria) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> Ver Informe
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Resumen</h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="detail-label">Checklist</div>
                            <div class="detail-value fw-bold">{{ $auditoria->checklistItems->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Conformes</div>
                            <div class="detail-value fw-bold text-success">{{ $auditoria->conformes_count }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Hallazgos</div>
                            <div class="detail-value fw-bold text-danger">{{ $auditoria->hallazgos_count }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Observaciones</div>
                            <div class="detail-value fw-bold text-warning">{{ $auditoria->observaciones_count }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Audit --}}
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $auditoria->creador ? $auditoria->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $auditoria->created_at ? $auditoria->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $auditoria->updated_at ? $auditoria->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
