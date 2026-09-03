@extends('layouts.app')

@section('title', 'Detalle del Reclamo')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-abierto { background: #fee2e2; color: #dc2626; }
    .badge-en_tramite { background: #fef3c7; color: #d97706; }
    .badge-resuelto { background: #dcfce7; color: #16a34a; }
    .badge-rechazado { background: #f1f5f9; color: #64748b; }
    .badge-cerrado { background: #dbeafe; color: #2563eb; }
    .badge-reclamo { background: #fee2e2; color: #dc2626; }
    .badge-queja { background: #fef3c7; color: #d97706; }
    .badge-sugerencia { background: #dbeafe; color: #2563eb; }
    .badge-cumpliment { background: #dcfce7; color: #16a34a; }
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
                    <h4 class="ui-header-title">{{ $reclamo->codigo }}</h4>
                    <div class="ui-header-meta">
                        <a href="{{ route('sgc.satisfaccion.reclamos') }}" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        {{ $reclamo->tipo_label }} — {{ $reclamo->canal_label }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-{{ $reclamo->estado }} fs-6">{{ $reclamo->estado_label }}</span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Detalles del Reclamo</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Código</div>
                            <div class="detail-value"><code>{{ $reclamo->codigo }}</code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Tipo</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $reclamo->tipo }}">{{ $reclamo->tipo_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Canal</div>
                            <div class="detail-value">{{ $reclamo->canal_label }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Cliente</div>
                            <div class="detail-value">{{ $reclamo->cliente ? $reclamo->cliente->nombre : '-' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-{{ $reclamo->estado }}">{{ $reclamo->estado_label }}</span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Asignado A</div>
                            <div class="detail-value">{{ $reclamo->asignado_a_label }}</div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $reclamo->descripcion }}</div>
                        </div>
                        @if($reclamo->resolucion)
                        <div class="col-12">
                            <div class="detail-label">Resolución</div>
                            <div class="detail-value">{{ $reclamo->resolucion }}</div>
                        </div>
                        @endif
                        @if($reclamo->fecha_resolucion)
                        <div class="col-md-6">
                            <div class="detail-label">Fecha Resolución</div>
                            <div class="detail-value">{{ $reclamo->fecha_resolucion->format('d/m/Y') }}</div>
                        </div>
                        @endif
                        @if($reclamo->satisfaccion_resolucion)
                        <div class="col-md-6">
                            <div class="detail-label">Satisfacción con Resolución</div>
                            <div class="detail-value text-warning">{{ $reclamo->satisfaccion_label }}</div>
                        </div>
                        @endif
                        @if($reclamo->tiempo_respuesta_horas)
                        <div class="col-md-6">
                            <div class="detail-label">Tiempo de Respuesta</div>
                            <div class="detail-value">{{ $reclamo->tiempo_respuesta_horas }} horas</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('sgc.satisfaccion.reclamos.update', $reclamo) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value">{{ $reclamo->creador ? $reclamo->creador->name : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value">{{ $reclamo->created_at ? $reclamo->created_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value">{{ $reclamo->updated_at ? $reclamo->updated_at->format('d/m/Y H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
