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
                    <h4 class="ui-header-title">Nueva Marca Tecnológica</h4>
                    <div class="ui-header-meta">Registra una nueva marca de productos tecnológicos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="marcaForm" action="{{ route('marcas-tecnologicas.store') }}" method="POST">
        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Marca *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label fw-bold">URL del Logo</label>
                            <input type="url" name="logo_url" id="logo_url" class="form-control @error('logo_url') is-invalid @enderror" value="{{ old('logo_url') }}" placeholder="https://ejemplo.com/logo.png">
                            @error('logo_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label fw-bold">Sitio Web</label>
                            <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website') }}" placeholder="https://ejemplo.com">
                            @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pais" class="form-label fw-bold">País</label>
                            <input type="text" name="pais" id="pais" class="form-control @error('pais') is-invalid @enderror" value="{{ old('pais') }}" placeholder="Ej: Estados Unidos, Japón, Corea del Sur">
                            @error('pais')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="contacto_email" class="form-label fw-bold">Email de Contacto</label>
                            <input type="email" name="contacto_email" id="contacto_email" class="form-control @error('contacto_email') is-invalid @enderror" value="{{ old('contacto_email') }}" placeholder="contacto@ejemplo.com">
                            @error('contacto_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="orden" class="form-label fw-bold">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="form-label fw-bold">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" form="marcaForm" class="ui-btn ui-btn-ghost rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>
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
