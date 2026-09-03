@extends('layouts.app')

@section('title', $vehiculoTipo->nombre)

@push('styles')
@include('partials.premium-ui')
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $vehiculoTipo->nombre }}</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-tag me-1"></i>
                        Tipo de vehículo
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('vehiculo-tipos.edit', $vehiculoTipo) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(59,130,246,.2);border-color:rgba(59,130,246,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="{{ route('vehiculo-tipos.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2" style="color:#3b82f6;"></i> Información del Tipo de Vehículo</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value fw-semibold">{{ $vehiculoTipo->nombre }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Código</div>
                            <div class="detail-value font-monospace">{{ $vehiculoTipo->codigo ?? '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value">{{ $vehiculoTipo->descripcion ?? 'Sin descripción' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Capacidad Máxima</div>
                            <div class="detail-value">{{ $vehiculoTipo->capacidad_maxima ? number_format($vehiculoTipo->capacidad_maxima, 2) . ' kg' : '—' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                @if($vehiculoTipo->activo ?? true)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Creación</div>
                            <div class="detail-value">{{ $vehiculoTipo->created_at->format('d/m/Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(59,130,246,.1);">
                        <i class="bi bi-truck fs-2" style="color:#3b82f6;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $vehiculoTipo->nombre }}</h5>
                    <small class="text-muted">{{ $vehiculoTipo->codigo ?? 'Sin código' }}</small>
                    <hr class="my-3">
                    <div class="text-start">
                        <small class="text-muted d-block">Creado: <span class="fw-semibold">{{ $vehiculoTipo->created_at->format('d/m/Y') }}</span></small>
                        <small class="text-muted d-block">Actualizado: <span class="fw-semibold">{{ $vehiculoTipo->updated_at->format('d/m/Y') }}</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
