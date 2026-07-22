@extends('layouts.app')
@section('title', 'Nuevo Proveedor')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nuevo Proveedor</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra un nuevo proveedor en el sistema
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('proveedores.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
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

    <form id="proveedorForm" action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">

                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-building me-2"></i>Información General
                    </h6>
                    <small class="text-muted">Datos básicos del proveedor</small>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <label class="ui-label">Nombre comercial <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Distribuidora Corripio">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" placeholder="(000) 000-0000">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Correo electrónico</label>
                        <input type="email" name="email" class="ui-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="correo@empresa.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección</label>
                        <input type="text" name="direccion" class="ui-input @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}" placeholder="Calle, sector, ciudad">
                        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-card-text me-2"></i>Información Fiscal
                    </h6>
                    <small class="text-muted">Datos tributarios del proveedor</small>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-4">
                        <label class="ui-label">RNC</label>
                        <input type="text" name="rnc" class="ui-input @error('rnc') is-invalid @enderror" value="{{ old('rnc') }}" placeholder="000-00000-0">
                        @error('rnc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Tipo de Persona</label>
                        <select name="tipo_persona" class="ui-select @error('tipo_persona') is-invalid @enderror">
                            <option value="">Seleccionar</option>
                            <option value="fisica" {{ old('tipo_persona') === 'fisica' ? 'selected' : '' }}>Física</option>
                            <option value="juridica" {{ old('tipo_persona') === 'juridica' ? 'selected' : '' }}>Jurídica</option>
                        </select>
                        @error('tipo_persona') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </h6>
                    <small class="text-muted">Opciones de retención fiscal</small>
                </div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_isr" value="1" id="ret_isr" {{ old('sujeto_retencion_isr') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_isr">Sujeto a Ret. ISR</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_itbis" value="1" id="ret_itbis" {{ old('sujeto_retencion_itbis') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small" for="ret_itbis">Sujeto a Ret. ITBIS</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" {{ old('activo', '1') === '1' ? 'checked' : '' }}>
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
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#4f46e5;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo proveedor</span>
        </div>
        <div>
            <a href="{{ route('proveedores.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="proveedorForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Proveedor
            </button>
        </div>
    </div>
</div>
@endsection
