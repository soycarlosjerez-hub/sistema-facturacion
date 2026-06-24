@extends('layouts.app')

@section('title', 'Editar Empresa de Delivery')

@push('styles')
<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #f97316;
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .sticky-save-bar .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #fb923c;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-pencil-square text-warning me-2"></i>
                        Editar Empresa de Delivery
                    </h2>
                    <p class="text-muted mb-0">{{ $company->nombre }}</p>
                </div>
                <a href="{{ route('delivery-companies.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
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

<div class="sticky-save-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando empresa: {{ $company->nombre }}
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
@endsection
