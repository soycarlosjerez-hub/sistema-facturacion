@extends('layouts.app')
@section('title', 'Ver Configuración de Garantía')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">{{ $garantiasConfig->nombre }}</h4>
                    <div class="ui-header-meta">Detalles de la configuración de garantía</div>
                </div>
            </div>
            <div class="ui-header-actions">
                @can('garantias-config.edit')
                <a href="{{ route('garantias-config.edit', $garantiasConfig) }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                @endcan
                <a href="{{ route('garantias-config.index') }}" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información de la Garantía</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong>{{ $garantiasConfig->nombre }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Producto</small>
                        @if($garantiasConfig->tipo_producto)
                        <span class="badge bg-info">{{ $garantiasConfig->tipo_producto }}</span>
                        @else
                        <span class="text-muted">General (todos los productos)</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Días de Garantía</small>
                        <span class="badge bg-success fs-6">{{ $garantiasConfig->dias_garantia }} días</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Garantía</small>
                        <span class="badge {{ $garantiasConfig->tipo_garantia == 'fabrica' ? 'bg-primary' : 'bg-warning text-dark' }} fs-6">
                            {{ $garantiasConfig->tipo_garantia_label }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden de Visualización</small>
                        <strong>{{ $garantiasConfig->orden }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge {{ $garantiasConfig->activo ? 'bg-success' : 'bg-secondary' }} fs-6">
                            {{ $garantiasConfig->activo_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Cobertura</h6>
                    @if($garantiasConfig->cobertura)
                    <p class="text-muted">{{ $garantiasConfig->cobertura }}</p>
                    @else
                    <p class="text-muted">Sin descripción de cobertura</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información Adicional</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Creada el</small>
                        <strong>{{ $garantiasConfig->created_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Última actualización</small>
                        <strong>{{ $garantiasConfig->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
