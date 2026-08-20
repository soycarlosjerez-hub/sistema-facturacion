@extends('layouts.app')
@section('title', 'Editar Marca Tecnológica')

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
                    <h4 class="ui-header-title">Editar Marca: {{ $marcaTecnologica->nombre }}</h4>
                    <div class="ui-header-meta">Actualiza los datos de la marca tecnológica</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('marcas-tecnologicas.update', $marcaTecnologica) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Marca *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $marcaTecnologica->nombre) }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="logo_url" class="form-label fw-bold">URL del Logo</label>
                            <input type="url" name="logo_url" id="logo_url" class="form-control @error('logo_url') is-invalid @enderror" value="{{ old('logo_url', $marcaTecnologica->logo_url) }}" placeholder="https://ejemplo.com/logo.png">
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label fw-bold">Sitio Web</label>
                            <input type="url" name="website" id="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $marcaTecnologica->website) }}" placeholder="https://ejemplo.com">
                        </div>

                        <div class="mb-3">
                            <label for="pais" class="form-label fw-bold">País</label>
                            <input type="text" name="pais" id="pais" class="form-control @error('pais') is-invalid @enderror" value="{{ old('pais', $marcaTecnologica->pais) }}" placeholder="Ej: Estados Unidos, Japón, Corea del Sur">
                        </div>

                        <div class="mb-3">
                            <label for="contacto_email" class="form-label fw-bold">Email de Contacto</label>
                            <input type="email" name="contacto_email" id="contacto_email" class="form-control @error('contacto_email') is-invalid @enderror" value="{{ old('contacto_email', $marcaTecnologica->contacto_email) }}" placeholder="contacto@ejemplo.com">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="orden" class="form-label fw-bold">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $marcaTecnologica->orden) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="form-label fw-bold">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" id="activo" {{ old('activo', $marcaTecnologica->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activa</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Marca
                            </button>
                            <a href="{{ route('marcas-tecnologicas.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
