@extends('layouts.app')
@section('title', 'Crear Especialidad Técnica')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Especialidad Técnica</h4>
                    <div class="ui-header-meta">Registra una nueva especialidad para técnicos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="especialidadForm" action="{{ route('tecnica-especialidades.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Especialidad *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                            <small class="text-muted">Ej: Redes, Impresoras, Servidores, CCTV, etc.</small>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label fw-bold">Descripción</label>
                            <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Describe las competencias necesarias para esta especialidad">{{ old('descripcion') }}</textarea>
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
                        <button type="submit" form="especialidadForm" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-check-lg me-1"></i>Guardar Especialidad
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
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva especialidad técnica</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tecnica-especialidades.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="especialidadForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Especialidad
            </button>
        </div>
    </div>
</div>
@endsection
