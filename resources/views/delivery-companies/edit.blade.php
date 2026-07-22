@extends('layouts.app')

@section('title', 'Editar Empresa de Delivery')

@push('styles')
@include('partials.premium-ui')
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
                    <h4 class="ui-header-title">Editar Empresa de Delivery</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span>{{ $company->nombre }}</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('delivery-companies.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <form method="POST" action="{{ route('delivery-companies.update', $company) }}" id="instanceForm">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="nombre" class="ui-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $company->nombre) }}" required maxlength="100">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nombre_corto" class="ui-label">Código <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_corto" id="nombre_corto" class="ui-input @error('nombre_corto') is-invalid @enderror"
                                   value="{{ old('nombre_corto', $company->nombre_corto) }}" required maxlength="30">
                            @error('nombre_corto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comision_porcentaje" class="ui-label">Comisión (%) <span class="text-danger">*</span></label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">%</span>
                                <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" id="comision_porcentaje"
                                       class="ui-input @error('comision_porcentaje') is-invalid @enderror"
                                       value="{{ old('comision_porcentaje', $company->comision_porcentaje) }}" required>
                                @error('comision_porcentaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" {{ $company->activo ? 'checked' : '' }}>
                            <label for="activo" class="form-check-label small fw-semibold">Activo</label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('delivery-companies.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection
