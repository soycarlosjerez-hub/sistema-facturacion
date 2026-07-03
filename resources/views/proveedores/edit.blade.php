@extends('layouts.app')

@section('title', 'Editar Proveedor')

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4" style="background:linear-gradient(135deg,#3b82f6,#6366f1,#8b5cf6,#3b82f6);box-shadow:0 8px 32px rgba(59,130,246,.25);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle" style="background:rgba(255,255,255,.2);border-color:rgba(255,255,255,.35);">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Editar Proveedor</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-building me-1"></i>
                        {{ $proveedore->nombre }}
                    </small>
                </div>
            </div>
            <a href="{{ route('proveedores.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <form id="proveedorForm" action="{{ route('proveedores.update', $proveedore->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="premium-card" style="animation-delay:.1s;">
            <div class="card-accent blue"></div>
            <div class="premium-card-title">
                <i class="bi bi-info-circle icon-blue"></i>
                Detalles del Proveedor
            </div>
            <div class="premium-card-subtitle">Actualiza la información del proveedor</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Nombre comercial <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $proveedore->nombre) }}" required placeholder="Ej. Distribuidora Corripio">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $proveedore->telefono) }}" placeholder="(000) 000-0000">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $proveedore->email) }}" placeholder="correo@empresa.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RNC</label>
                        <input type="text" name="rnc" class="form-control @error('rnc') is-invalid @enderror" value="{{ old('rnc', $proveedore->rnc) }}" placeholder="000-00000-0">
                        @error('rnc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tipo de Persona</label>
                        <select name="tipo_persona" class="form-select @error('tipo_persona') is-invalid @enderror">
                            <option value="">Seleccionar</option>
                            <option value="fisica" {{ old('tipo_persona', $proveedore->tipo_persona) === 'fisica' ? 'selected' : '' }}>Física</option>
                            <option value="juridica" {{ old('tipo_persona', $proveedore->tipo_persona) === 'juridica' ? 'selected' : '' }}>Jurídica</option>
                        </select>
                        @error('tipo_persona') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $proveedore->direccion) }}" placeholder="Calle, sector, ciudad">
                        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_isr" value="1" id="ret_isr" {{ $proveedore->sujeto_retencion_isr ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_isr">Sujeto a Ret. ISR</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_itbis" value="1" id="ret_itbis" {{ $proveedore->sujeto_retencion_itbis ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_itbis">Sujeto a Ret. ITBIS</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" {{ $proveedore->activo ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="chk-activo">Proveedor Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-end align-items-center">
        <a href="{{ route('proveedores.index') }}" class="btn-cancel me-2">Cancelar</a>
        <button type="submit" form="proveedorForm" class="btn-save">
            <i class="bi bi-check-lg me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
@endsection
