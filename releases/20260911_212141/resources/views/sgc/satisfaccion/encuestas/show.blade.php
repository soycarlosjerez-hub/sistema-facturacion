@extends('layouts.app')

@section('title', 'Encuesta de Satisfacción')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-borrador { background: #f1f5f9; color: #64748b; }
    .badge-activa { background: #dcfce7; color: #16a34a; }
    .badge-cerrada { background: #dbeafe; color: #2563eb; }
    .pregunta-card { border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; }
    .respuesta-bar { height: 8px; border-radius: 4px; }
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
                    <i class="bi bi-clipboard-data"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $encuesta->titulo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.satisfaccion.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Encuesta de satisfacción del cliente
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $encuesta->estado }} fs-6">{{ $encuesta->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            {{-- Info --}}
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Encuesta</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Fecha Inicio</div>
                            <div class="detail-value">{{ $encuesta->fecha_inicio_label }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Fecha Fin</div>
                            <div class="detail-value">{{ $encuesta->fecha_fin_label }}</div>
                        </div>
                        @if($encuesta->descripcion)
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $encuesta->descripcion }}</div>
                        </div>
                        @endif
                        @if($encuesta->instrucciones)
                        <div class="col-12">
                            <div class="detail-label">Instrucciones</div>
                            <div class="detail-value">{{ $encuesta->instrucciones }}</div>
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="detail-label">Preguntas</div>
                            <div class="detail-value fw-bold">{{ $encuesta->pregunta_count }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Respuestas</div>
                            <div class="detail-value fw-bold text-success">{{ $encuesta->respuestas_clientes_count }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Promedio</div>
                            <div class="detail-value fw-bold">{{ $encuesta->promedio_puntuacion ? number_format($encuesta->promedio_puntuacion, 1) : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Preguntas --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-question me-2"></i>Preguntas ({{ $encuesta->preguntas->count() }})</h6>
                    @if($encuesta->preguntas->count())
                        @foreach($encuesta->preguntas as $pregunta)
                        <div class="pregunta-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold small">{{ $pregunta->texto ?? $pregunta->enunciado ?? 'Pregunta #' . $pregunta->id }}</span>
                                @if($pregunta->obligatoria)
                                <span class="badge bg-danger bg-opacity-10 text-danger" style="font-size:.6rem;">Obligatoria</span>
                                @endif
                            </div>
                            @if(isset($pregunta->tipo))
                            <small class="text-muted">Tipo: {{ $pregunta->tipo }}</small>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay preguntas configuradas.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @if($encuesta->estaActiva())
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body text-center">
                    <i class="bi bi-reply-all-fill text-success" style="font-size:2rem;"></i>
                    <h6 class="fw-bold mt-2">Encuesta Activa</h6>
                    <p class="text-muted small">Comparte el enlace para recibir respuestas.</p>
                    <a href="{{ route('sgc.satisfaccion.show', $encuesta) }}" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                        <i class="bi bi-link-45deg me-1"></i> Copiar Enlace
                    </a>
                </div>
            </div>
            @endif

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $encuesta->creador ? $encuesta->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $encuesta->created_at ? $encuesta->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $encuesta->updated_at ? $encuesta->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
