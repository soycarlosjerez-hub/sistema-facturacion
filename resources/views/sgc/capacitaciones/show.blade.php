@extends('layouts.app')

@section('title', 'Capacitación')

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
    .badge-presencial { background: #dbeafe; color: #2563eb; }
    .badge-virtual { background: #e0f2fe; color: #0284c7; }
    .badge-hibrido { background: #fef3c7; color: #d97706; }
    .participante-row { border-bottom: 1px solid #f1f5f9; padding: .75rem 0; }
    .participante-row:last-child { border-bottom: none; }
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
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $capacitacion->titulo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.capacitaciones.index') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Detalle de la capacitación
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $capacitacion->estado }} fs-6">{{ $capacitacion->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Capacitación</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Fecha</div>
                            <div class="detail-value">{{ $capacitacion->fecha ? $capacitacion->fecha->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Horario</div>
                            <div class="detail-value">{{ $capacitacion->horario_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Modalidad</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $capacitacion->modalidad }}">{{ $capacitacion->modalidad_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Duración</div>
                            <div class="detail-value">{{ $capacitacion->duracion_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Lugar</div>
                            <div class="detail-value">{{ $capacitacion->lugar ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Instructor</div>
                            <div class="detail-value">{{ $capacitacion->instructor_label }}</div>
                        </div>
                        @if($capacitacion->temas)
                        <div class="col-12">
                            <div class="detail-label">Temas</div>
                            <div class="detail-value">{{ $capacitacion->temas }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Participantes --}}
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Participantes ({{ $capacitacion->participantes->count() }})</h6>
                    @if($capacitacion->participantes->count())
                        @foreach($capacitacion->participantes as $part)
                        <div class="participante-row">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold small">{{ $part->usuario ? $part->usuario->name : 'Usuario #' . $part->usuario_id }}</span>
                                    @if(isset($part->estado))
                                    <span class="badge-status badge-{{ $part->estado === 'asistio' ? 'completada' : 'programada' }} ms-1">{{ $part->estado }}</span>
                                    @endif
                                </div>
                                @if(isset($part->puntuacion))
                                <span class="text-warning small">
                                    @for($i = 1; $i <= 5; $i++)★@endfor
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small mb-0">No hay participantes inscritos.</p>
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
                        <a href="{{ route('sgc.capacitaciones.edit', $capacitacion) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Estadísticas</h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="detail-label">Inscritos</div>
                            <div class="detail-value fw-bold">{{ $capacitacion->participantes->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Asistieron</div>
                            <div class="detail-value fw-bold text-success">{{ $capacitacion->asistencia_count }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Calificación Promedio</div>
                            <div class="detail-value fw-bold">{{ $capacitacion->promedio_calificacion ? number_format($capacitacion->promedio_calificacion, 1) : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $capacitacion->creador ? $capacitacion->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $capacitacion->created_at ? $capacitacion->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $capacitacion->updated_at ? $capacitacion->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
