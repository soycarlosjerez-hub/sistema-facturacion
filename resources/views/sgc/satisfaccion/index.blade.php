@extends('layouts.app')

@section('title', 'Satisfacción del Cliente')

@push('styles')
@include('partials.premium-ui')
<style>
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-borrador { background: #f1f5f9; color: #64748b; }
    .badge-activa { background: #dcfce7; color: #16a34a; }
    .badge-cerrada { background: #dbeafe; color: #2563eb; }
    .badge-abierto { background: #fee2e2; color: #dc2626; }
    .badge-en_tramite { background: #fef3c7; color: #d97706; }
    .badge-resuelto { background: #dcfce7; color: #16a34a; }
    .badge-cerrado { background: #dbeafe; color: #2563eb; }
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
                    <i class="bi bi-emoji-smile"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Satisfacción del Cliente</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i> Encuestas de satisfacción y gestión de reclamos
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.satisfaccion.reclamos') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill me-2">
                    <i class="bi bi-exclamation-triangle me-1"></i> Reclamos
                </a>
                <a href="{{ route('sgc.satisfaccion.create') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Encuesta
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:0s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-value">{{ $stats['total_encuestas'] ?? 0 }}</div>
                            <div class="ui-stat-label">Encuestas</div>
                        </div>
                        <div class="ui-stat-icon" style="background:rgba(99,102,241,.15);color:#6366f1;">
                            <i class="bi bi-clipboard-data"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.05s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-value" style="color:#22c55e;">{{ $stats['encuestas_activas'] ?? 0 }}</div>
                            <div class="ui-stat-label">Activas</div>
                        </div>
                        <div class="ui-stat-icon" style="background:rgba(34,197,94,.15);color:#22c55e;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="--accent:#ef4444;--accent-hover:#dc2626;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-value" style="color:#ef4444;">{{ $stats['total_reclamos'] ?? 0 }}</div>
                            <div class="ui-stat-label">Reclamos</div>
                        </div>
                        <div class="ui-stat-icon" style="background:rgba(239,68,68,.15);color:#ef4444;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ui-stat-value" style="color:#f59e0b;">{{ $stats['reclamos_abiertos'] ?? 0 }}</div>
                            <div class="ui-stat-label">Reclamos Abiertos</div>
                        </div>
                        <div class="ui-stat-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Encuestas --}}
    <div class="ui-card mb-4" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-clipboard-data me-2"></i>Encuestas de Satisfacción</h6>
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Preguntas</th>
                            <th>Respuestas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($encuestas ?? [] as $enc)
                        <tr>
                            <td>{{ Str::limit($enc->titulo, 40) }}</td>
                            <td>{{ $enc->fecha_inicio_label }}</td>
                            <td>{{ $enc->fecha_fin_label }}</td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.7rem;">{{ $enc->pregunta_count }}</span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success" style="font-size:.7rem;">{{ $enc->respuestas_clientes_count }}</span></td>
                            <td><span class="badge-status badge-{{ $enc->estado }}">{{ $enc->estado_label }}</span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('sgc.satisfaccion.show', $enc) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($enc->estaActiva())
                                    <a href="{{ route('sgc.satisfaccion.show', $enc) }}" class="btn btn-sm btn-outline-success rounded-pill" title="Responder">
                                        <i class="bi bi-reply"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">No hay encuestas registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
