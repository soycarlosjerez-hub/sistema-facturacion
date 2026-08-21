@extends('layouts.app')
@section('title', 'Nueva Marca Tecnológica')

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
                    <i class="bi bi-tag"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nueva Marca Tecnológica</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva marca de productos tecnológicos
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('marcas-tecnologicas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="marcaForm" action="{{ route('marcas-tecnologicas.store') }}" method="POST">
                    @csrf

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #2563eb;">
                            <i class="bi bi-info-circle me-2"></i>Información Básica
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nombre" class="ui-label">Nombre de la Marca <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="logo_url" class="ui-label">URL del Logo</label>
                                <input type="url" name="logo_url" id="logo_url" class="ui-input @error('logo_url') is-invalid @enderror" value="{{ old('logo_url') }}" placeholder="https://ejemplo.com/logo.png">
                                @error('logo_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="ui-label">Sitio Web</label>
                                <input type="url" name="website" id="website" class="ui-input @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://ejemplo.com">
                                @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="pais" class="ui-label">País</label>
                                <input type="text" name="pais" id="pais" class="ui-input @error('pais') is-invalid @enderror" value="{{ old('pais') }}" placeholder="Ej: Estados Unidos, Japón, Corea del Sur">
                                @error('pais')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="contacto_email" class="ui-label">Email de Contacto</label>
                                <input type="email" name="contacto_email" id="contacto_email" class="ui-input @error('contacto_email') is-invalid @enderror" value="{{ old('contacto_email') }}" placeholder="contacto@ejemplo.com">
                                @error('contacto_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #059669;">
                            <i class="bi bi-gear me-2"></i>Configuración
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="orden" class="ui-label">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="ui-input @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(59,130,246,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', true) ? 'checked' : '' }} role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="activo">Activa</label>
                                    </div>
                                    <small class="text-muted">Si está inactiva no aparecerá en las listas.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#3b82f6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva marca</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('marcas-tecnologicas.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="marcaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Marca
            </button>
        </div>
    </div>
</div>
@endsection
