@extends('layouts.app')
@section('title', 'Nueva Empresa de Delivery')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Empresa de Delivery</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una plataforma de delivery externa
                    </small>
                </div>
            </div>
            <a href="{{ route('delivery-companies.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-1"></i> Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
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
<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#10b981;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva empresa de delivery</span>
        </div>
        <div>
            <a href="{{ route('delivery-companies.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="deliveryForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Empresa
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush
