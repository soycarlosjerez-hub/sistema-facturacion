@extends('layouts.app')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
    position: relative;
    overflow: hidden;
}
.premium-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #f59e0b;
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
    border-top-color: #fbbf24;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
.input-group-lg .form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25) !important;
    border-color: #fbbf24;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-person-plus me-2"></i>Nuevo Proveedor</h3>
                <p class="mb-0 opacity-75">Registra un nuevo proveedor en el sistema</p>
            </div>
            <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <div class="card-header bg-white border-bottom border-light p-4">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-warning me-2"></i>Información del Proveedor</h5>
        </div>

        <form id="proveedorForm" action="{{ route('proveedores.store') }}" method="POST">
            @csrf

            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-4 p-3 rounded-3" style="background: rgba(245, 158, 11, 0.08); border-left: 4px solid #f59e0b;">
                    <i class="bi bi-info-circle text-warning me-2"></i>
                    <span class="text-muted small">Creando nuevo proveedor</span>
                </div>

                <div class="row g-4">
                    <!-- Nombre Comercial -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Nombre comercial <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-building"></i></span>
                            <input type="text" name="nombre" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('nombre') }}" required placeholder="Ej. Distribuidora Corripio">
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Teléfono</label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                            <input type="text" name="telefono" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('telefono') }}" placeholder="(000) 000-0000">
                        </div>
                    </div>

                    <!-- Correo electrónico -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Correo electrónico</label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('email') }}" placeholder="correo@empresa.com">
                        </div>
                    </div>

                    <!-- RNC -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">RNC</label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" name="rnc" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('rnc') }}" placeholder="000-00000-0">
                        </div>
                        @error('rnc')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <!-- Dirección -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Dirección</label>
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" name="direccion" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('direccion') }}" placeholder="Calle, sector, ciudad">
                        </div>
                    </div>

                    <!-- Tipo Persona -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Tipo de Persona</label>
                        <select name="tipo_persona" class="form-select form-select-lg shadow-sm rounded-3">
                            <option value="">Seleccionar</option>
                            <option value="fisica" {{ old('tipo_persona') === 'fisica' ? 'selected' : '' }}>Física</option>
                            <option value="juridica" {{ old('tipo_persona') === 'juridica' ? 'selected' : '' }}>Jurídica</option>
                        </select>
                    </div>

                    <!-- Retenciones -->
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_isr" value="1" id="ret_isr" {{ old('sujeto_retencion_isr') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_isr">Sujeto a Ret. ISR</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_itbis" value="1" id="ret_itbis" {{ old('sujeto_retencion_itbis') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_itbis">Sujeto a Ret. ITBIS</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="sticky-save-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
        <button type="submit" form="proveedorForm" class="btn btn-primary rounded-pill px-5 shadow fw-bold" style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none;">
            <i class="bi bi-plus-circle me-2"></i>Guardar Proveedor
        </button>
    </div>
</div>
@endsection
