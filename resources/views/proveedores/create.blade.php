@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;"><i class="bi bi-person-plus text-primary me-2"></i>Nuevo Proveedor</h2>
                    <p class="text-muted mb-0">Registra un nuevo proveedor en el sistema</p>
                </div>
                <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Información del Proveedor</h5>
                </div>

                <form action="{{ route('proveedores.store') }}" method="POST">
                    @csrf

                    <div class="card-body p-4 p-md-5">
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

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold" style="transition: all 0.3s ease;">
                            <i class="bi bi-plus-circle me-2"></i>Guardar Proveedor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-lg .form-control:focus {
        border-color: #dee2e6;
        box-shadow: none;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        border-color: #86b7fe;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
</style>
@endsection
