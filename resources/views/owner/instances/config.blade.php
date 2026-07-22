@extends('layouts.app')
@section('title', 'Configuración - ' . $instance->nombre)

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Configuraci&oacute;n de Instancia</h2>
                    <p class="mb-0 opacity-75">{{ $instance->nombre }} &middot; {{ $instance->businessType?->nombre ?? 'Sin tipo' }}</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#3b82f6"></div>
                <div class="card-header bg-transparent border-0 p-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-sliders text-warning me-2"></i>Par&aacute;metros de Configuraci&oacute;n
                    </h5>
                    <small class="text-muted">Estos valores sobreescriben la configuraci&oacute;n global para esta instancia.</small>
                </div>
                <div class="card-body p-4 pt-0">
                    <form method="POST" action="{{ route('owner.instances.config.update', $instance) }}">
                        @csrf @method('PUT')

                        <div class="alert alert-info rounded-4 border-0 bg-info bg-opacity-10" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            Los valores marcados con <span class="ui-badge ui-badge-warning rounded-pill">personalizado</span> son espec&iacute;ficos de esta instancia. Los que tienen <span class="ui-badge ui-badge-neutral rounded-pill">global</span> usan la configuraci&oacute;n del sistema.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    Nombre de la Empresa
                                    @if(isset($instanceConfig['nombre_empresa']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="text" name="nombre_empresa" class="ui-input rounded-pill @error('nombre_empresa') is-invalid @enderror" value="{{ old('nombre_empresa', $instanceConfig['nombre_empresa'] ?? '') }}" placeholder="{{ $globalSettings['nombre_empresa'] ?: 'Global: (vacio)' }}">
                                @if(!isset($instanceConfig['nombre_empresa']) && $globalSettings['nombre_empresa'])
                                    <small class="text-muted">Global: {{ $globalSettings['nombre_empresa'] }}</small>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    Slogan
                                    @if(isset($instanceConfig['slogan']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="text" name="slogan" class="ui-input rounded-pill @error('slogan') is-invalid @enderror" value="{{ old('slogan', $instanceConfig['slogan'] ?? '') }}" placeholder="{{ $globalSettings['slogan'] ?: 'Global: (vacio)' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    S&iacute;mbolo de Moneda
                                    @if(isset($instanceConfig['moneda_simbolo']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="text" name="moneda_simbolo" class="ui-input rounded-pill @error('moneda_simbolo') is-invalid @enderror" value="{{ old('moneda_simbolo', $instanceConfig['moneda_simbolo'] ?? '') }}" placeholder="{{ $globalSettings['moneda_simbolo'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    ITBS %
                                    @if(isset($instanceConfig['itbis_porcentaje']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="number" name="itbis_porcentaje" class="ui-input rounded-pill @error('itbis_porcentaje') is-invalid @enderror" value="{{ old('itbis_porcentaje', $instanceConfig['itbis_porcentaje'] ?? '') }}" step="0.01" min="0" max="100" placeholder="{{ $globalSettings['itbis_porcentaje'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    Prefijo Factura
                                    @if(isset($instanceConfig['prefijo_factura']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="text" name="prefijo_factura" class="ui-input rounded-pill @error('prefijo_factura') is-invalid @enderror" value="{{ old('prefijo_factura', $instanceConfig['prefijo_factura'] ?? '') }}" placeholder="{{ $globalSettings['prefijo_factura'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    Prefijo NCF
                                    @if(isset($instanceConfig['prefijo_ncf']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="text" name="prefijo_ncf" class="ui-input rounded-pill @error('prefijo_ncf') is-invalid @enderror" value="{{ old('prefijo_ncf', $instanceConfig['prefijo_ncf'] ?? '') }}" placeholder="{{ $globalSettings['prefijo_ncf'] }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label fw-bold">
                                    D&iacute;as de Cr&eacute;dito
                                    @if(isset($instanceConfig['dias_credito']))
                                        <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                    @else
                                        <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                    @endif
                                </label>
                                <input type="number" name="dias_credito" class="ui-input rounded-pill @error('dias_credito') is-invalid @enderror" value="{{ old('dias_credito', $instanceConfig['dias_credito'] ?? '') }}" min="0" max="365" placeholder="{{ $globalSettings['dias_credito'] }}">
                            </div>
                        </div>

                        <hr>
                        <h6 class="fw-bold text-muted mb-3"><i class="bi bi-shop me-2"></i>M&oacute;dulo Restaurante</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-4 border" style="background: rgba(16,185,129,0.04); border-color: rgba(16,185,129,0.2) !important;">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <label class="ui-label fw-bold small mb-0">
                                                Validar Stock en Restaurante
                                                @if(isset($instanceConfig['restaurante_valida_stock']))
                                                    <span class="ui-badge ui-badge-warning rounded-pill ms-1" style="font-size:.55rem;">personalizado</span>
                                                @else
                                                    <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">global</span>
                                                @endif
                                            </label>
                                            <small class="text-muted d-block" style="font-size:.72rem;">
                                                Si est&aacute; activo, solo se muestran productos con stock disponible.
                                            </small>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="restaurante_valida_stock" value="1"
                                                   {{ ($instanceConfig['restaurante_valida_stock'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Deja en blanco para usar el valor global.
                            </small>
                            <div class="d-flex gap-2">
                                <a href="{{ route('owner.instances.show', $instance) }}" class="ui-btn ui-btn-primary rounded-pill px-4">Cancelar</a>
                                <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold">
                                    <i class="bi bi-save me-2"></i>Guardar Configuraci&oacute;n
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
