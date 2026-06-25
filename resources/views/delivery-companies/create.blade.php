@extends('layouts.app')
@section('title', 'Nueva Empresa de Delivery')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .sticky-save-bar {
    background: #0f172a;
    border-top-color: #34d399;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
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
                    <h2 class="fw-bold mb-0 text-white">Nueva Empresa de Delivery</h2>
                    <p class="text-white text-opacity-75 mb-0">Registra una plataforma de delivery externa</p>
                </div>
            </div>
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-light rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="premium-card mb-5">
        <div class="card-accent green"></div>
        <form id="deliveryForm" method="POST" action="{{ route('delivery-companies.store') }}">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #10b981;">
                        <i class="bi bi-info-circle me-2"></i>Información de la Empresa
                    </h6>
                </div>
                <div class="mb-3">
                    <label for="nombre" class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" required maxlength="100" placeholder="Ej: Uber Eats">
                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="mb-3">
                    <label for="nombre_corto" class="form-label small fw-semibold">Código <span class="text-danger">*</span></label>
                    <input type="text" name="nombre_corto" id="nombre_corto" class="form-control @error('nombre_corto') is-invalid @enderror"
                           value="{{ old('nombre_corto') }}" required maxlength="30" placeholder="Ej: uber_eats">
                    @error('nombre_corto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">Identificador único usado internamente (snake_case).</div>
                </div>
                <div class="mb-3">
                    <label for="comision_porcentaje" class="form-label small fw-semibold">Comisión (%) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" id="comision_porcentaje"
                               class="form-control @error('comision_porcentaje') is-invalid @enderror"
                               value="{{ old('comision_porcentaje') }}" required>
                        <span class="input-group-text">%</span>
                        @error('comision_porcentaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" checked>
                    <label for="activo" class="form-check-label small fw-semibold">Activo</label>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-none d-md-flex align-items-center gap-2">
            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                <i class="bi bi-hourglass-split me-1"></i> Creando nueva empresa de delivery
            </span>
        </div>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-cancel rounded-pill px-4">Cancelar</a>
            <button type="submit" form="deliveryForm" class="btn btn-save rounded-pill px-4 shadow-sm">
                <i class="bi bi-check-lg me-1"></i> Guardar Empresa
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
