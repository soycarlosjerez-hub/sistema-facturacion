@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #0f172a;"><i class="bi bi-pencil-square text-primary me-2"></i>Editar Proveedor</h2>
                    <p class="text-muted mb-0">Actualiza la información comercial de tu proveedor</p>
                </div>
                <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <!-- Glassmorphism Alert -->
            <div class="alert border-0 rounded-4 shadow-sm mb-4" style="background: rgba(13, 110, 253, 0.05); border-left: 4px solid #0d6efd !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: d-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-info-circle fs-5"></i>
                    </div>
                    <div>
                        <span class="text-muted">Estás editando el perfil de:</span>
                        <strong class="text-dark d-block" style="font-size: 1.1rem;">{{ $proveedore->nombre }}</strong>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark">Detalles del Proveedor</h5>
                </div>

                <form action="{{ route('proveedores.update', $proveedore->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <!-- Nombre Comercial -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Nombre comercial <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-building"></i></span>
                                    <input type="text" name="nombre" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('nombre', $proveedore->nombre) }}" required placeholder="Ej. Distribuidora Corripio">
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Teléfono</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="telefono" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('telefono', $proveedore->telefono) }}" placeholder="(000) 000-0000">
                                </div>
                            </div>

                            <!-- Correo electrónico -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Correo electrónico</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('email', $proveedore->email) }}" placeholder="correo@empresa.com">
                                </div>
                            </div>

                            <!-- RNC -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">RNC</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-upc-scan"></i></span>
                                    <input type="text" name="rnc" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('rnc', $proveedore->rnc) }}" placeholder="000-00000-0">
                                </div>
                                @error('rnc')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <!-- Dirección -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Dirección</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-geo-alt"></i></span>
                                    <input type="text" name="direccion" class="form-control border-start-0 ps-0 form-control-lg bg-white" value="{{ old('direccion', $proveedore->direccion) }}" placeholder="Calle, sector, ciudad">
                                </div>
                            </div>

                            <!-- Tipo Persona -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Tipo de Persona</label>
                                <select name="tipo_persona" class="form-select form-select-lg shadow-sm rounded-3">
                                    <option value="">Seleccionar</option>
                                    <option value="fisica" {{ old('tipo_persona', $proveedore->tipo_persona) === 'fisica' ? 'selected' : '' }}>Física</option>
                                    <option value="juridica" {{ old('tipo_persona', $proveedore->tipo_persona) === 'juridica' ? 'selected' : '' }}>Jurídica</option>
                                </select>
                            </div>

                            <!-- Retenciones -->
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="sujeto_retencion_isr" value="1" id="ret_isr" {{ $proveedore->sujeto_retencion_isr ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="ret_isr">Sujeto a Ret. ISR</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" name="sujeto_retencion_itbis" value="1" id="ret_itbis" {{ $proveedore->sujeto_retencion_itbis ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold small" for="ret_itbis">Sujeto a Ret. ITBIS</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-lg fw-bold" style="transition: all 0.3s ease;">
                            <i class="bi bi-cloud-arrow-up me-2"></i>Guardar Cambios
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
