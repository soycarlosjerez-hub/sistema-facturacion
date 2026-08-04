@extends('layouts.app')

@section('title', 'Nueva Zona de Cobertura')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Zona de Cobertura</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-map me-1"></i>
                        <span>Definir una nueva zona de reparto</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('delivery-zones.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="bi bi-exclamation-circle me-1"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="zoneForm" method="POST" action="{{ route('delivery-zones.store') }}">
            @csrf
            <div class="ui-card-body">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-info-circle me-2"></i>Información General
                    </h6>
                </div>

                <div class="mb-3">
                    <label for="nombre" class="ui-label">Nombre de la Zona <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" required maxlength="100" placeholder="Ej: Zona Norte, Centro Histórico">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="ui-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="ui-textarea @error('descripcion') is-invalid @enderror"
                              rows="2" maxlength="300" placeholder="Describe los límites o características de la zona...">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-rulers me-2"></i>Parámetros de Entrega
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="radio_km" class="ui-label">Radio (km) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <input type="number" step="0.1" min="0.1" name="radio_km" id="radio_km" class="ui-input @error('radio_km') is-invalid @enderror"
                                   value="{{ old('radio_km') }}" required placeholder="5">
                            <span class="ui-input-group-text">km</span>
                        </div>
                        @error('radio_km') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="tiempo_estimado_minutos" class="ui-label">Tiempo Estimado (min) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <input type="number" min="1" name="tiempo_estimado_minutos" id="tiempo_estimado_minutos" class="ui-input @error('tiempo_estimado_minutos') is-invalid @enderror"
                                   value="{{ old('tiempo_estimado_minutos') }}" required placeholder="30">
                            <span class="ui-input-group-text">min</span>
                        </div>
                        @error('tiempo_estimado_minutos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="minimo_para_envio_gratis" class="ui-label">Envío Gratis (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="minimo_para_envio_gratis" id="minimo_para_envio_gratis" class="ui-input @error('minimo_para_envio_gratis') is-invalid @enderror"
                                   value="{{ old('minimo_para_envio_gratis', 0) }}" placeholder="0">
                        </div>
                        @error('minimo_para_envio_gratis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Monto mínimo para envío gratuito en esta zona</div>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-cash-coin me-2"></i>Tarifas
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tarifa_base" class="ui-label">Tarifa Base (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="tarifa_base" id="tarifa_base" class="ui-input @error('tarifa_base') is-invalid @enderror"
                                   value="{{ old('tarifa_base') }}" required placeholder="150.00">
                        </div>
                        @error('tarifa_base') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tarifa_por_km" class="ui-label">Tarifa por Km (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="tarifa_por_km" id="tarifa_por_km" class="ui-input @error('tarifa_por_km') is-invalid @enderror"
                                   value="{{ old('tarifa_por_km') }}" required placeholder="25.00">
                        </div>
                        @error('tarifa_por_km') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-check form-switch mt-4">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" {{ old('activo', true) ? 'checked' : '' }}>
                    <label for="activo" class="form-check-label small fw-semibold">Zona Activa</label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('delivery-zones.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
        <button type="submit" form="zoneForm" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
            <i class="bi bi-check-lg me-2"></i>Guardar Zona
        </button>
    </div>
</div>
@endsection
