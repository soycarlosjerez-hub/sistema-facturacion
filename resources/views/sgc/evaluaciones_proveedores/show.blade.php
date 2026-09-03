@extends('layouts.app')

@section('title', 'Evaluación de Proveedor')

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-pendiente { background: #fef3c7; color: #d97706; }
    .badge-aprobado { background: #dcfce7; color: #16a34a; }
    .badge-rechazado { background: #fee2e2; color: #dc2626; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

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
                    <h4 class="ui-header-title">{{ $evaluacion->codigo }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        {{ $evaluacion->proveedor?->nombre ?? 'Sin proveedor' }}
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sgc.evaluaciones-proveedores.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2" style="color:#8b5cf6;"></i> Información de la Evaluación</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Código</div>
                            <div class="detail-value fw-bold font-monospace">{{ $evaluacion->codigo }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Proveedor</div>
                            <div class="detail-value fw-semibold">{{ $evaluacion->proveedor?->nombre ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha</div>
                            <div class="detail-value">{{ $evaluacion->fecha ? $evaluacion->fecha->format('d/m/Y') : '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                <span class="badge-status badge-{{ $evaluacion->estado ?? 'pendiente' }}">{{ ucfirst($evaluacion->estado ?? 'pendiente') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Criterio de Evaluación</div>
                            <div class="detail-value">{{ $evaluacion->criterio ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Puntuación</div>
                            <div class="detail-value fw-bold" style="font-size:1.2rem;color:{{ ($evaluacion->puntuacion ?? 0) >= 70 ? '#16a34a' : (($evaluacion->puntuacion ?? 0) >= 50 ? '#d97706' : '#dc2626') }};">
                                {{ $evaluacion->puntuacion ?? '—' }}/100
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Observaciones</div>
                            <div class="detail-value">{{ $evaluacion->observaciones ?? 'Sin observaciones' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Evaluado por</div>
                            <div class="detail-value">{{ $evaluacion->evaluador?->name ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Creación</div>
                            <div class="detail-value">{{ $evaluacion->created_at->format('d/m/Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(139,92,246,.1);">
                        <i class="bi bi-clipboard-check fs-2" style="color:#8b5cf6;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $evaluacion->codigo }}</h5>
                    <small class="text-muted">{{ $evaluacion->proveedor?->nombre ?? 'Sin proveedor' }}</small>
                    <hr class="my-3">
                    <div class="text-start">
                        <small class="text-muted d-block">Estado: <span class="badge-status badge-{{ $evaluacion->estado ?? 'pendiente' }}">{{ ucfirst($evaluacion->estado ?? 'pendiente') }}</span></small>
                    </div>
                </div>
            </div>

            @if($evaluacion->puntuacion)
            <div class="ui-card mt-4" style="--delay:.2s">
                <div class="ui-card-accent" style="background:{{ ($evaluacion->puntuacion ?? 0) >= 70 ? '#16a34a' : (($evaluacion->puntuacion ?? 0) >= 50 ? '#d97706' : '#dc2626') }}"></div>
                <div class="ui-card-body text-center">
                    <h6 class="fw-bold mb-2">Resultado</h6>
                    <div class="fw-bold" style="font-size:2rem;color:{{ ($evaluacion->puntuacion ?? 0) >= 70 ? '#16a34a' : (($evaluacion->puntuacion ?? 0) >= 50 ? '#d97706' : '#dc2626') }};">
                        {{ $evaluacion->puntuacion }}/100
                    </div>
                    <small class="text-muted">
                        @if(($evaluacion->puntuacion ?? 0) >= 70)
                            <i class="bi bi-check-circle-fill text-success me-1"></i>Aprobado
                        @elseif(($evaluacion->puntuacion ?? 0) >= 50)
                            <i class="bi bi-exclamation-circle-fill text-warning me-1"></i>Mejorable
                        @else
                            <i class="bi bi-x-circle-fill text-danger me-1"></i>No cumple
                        @endif
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
