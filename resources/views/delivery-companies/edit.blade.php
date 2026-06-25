@extends('layouts.app')

@section('title', 'Editar Empresa de Delivery')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid py-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-0 text-white">Editar Empresa de Delivery</h2>
                    <p class="text-white text-opacity-75 mb-0">{{ $company->nombre }}</p>
                </div>
            </div>
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
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

            <div class="premium-card">
                <div class="card-accent green"></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('delivery-companies.update', $company) }}" id="instanceForm">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre', $company->nombre) }}" required maxlength="100">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nombre_corto" class="form-label small fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_corto" id="nombre_corto" class="form-control @error('nombre_corto') is-invalid @enderror"
                                   value="{{ old('nombre_corto', $company->nombre_corto) }}" required maxlength="30">
                            @error('nombre_corto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="comision_porcentaje" class="form-label small fw-semibold">Comisión (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" id="comision_porcentaje"
                                       class="form-control @error('comision_porcentaje') is-invalid @enderror"
                                       value="{{ old('comision_porcentaje', $company->comision_porcentaje) }}" required>
                                <span class="input-group-text">%</span>
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

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando empresa: {{ $company->nombre }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-cancel rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-save rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection
