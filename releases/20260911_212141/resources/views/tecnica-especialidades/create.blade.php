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
                    <div class="ui-header-title">Nueva Especialidad Técnica</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva especialidad para técnicos
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('tecnica-especialidades.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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
                <form id="especialidadForm" action="{{ route('tecnica-especialidades.store') }}" method="POST">
                    @csrf

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nombre" class="ui-label">Nombre de la Especialidad <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                                <small class="text-muted">Ej: Redes, Impresoras, Servidores, CCTV, etc.</small>
                                @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="descripcion" class="ui-label">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="ui-textarea @error('descripcion') is-invalid @enderror" rows="3" placeholder="Describe las competencias necesarias para esta especialidad">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="orden" class="ui-label">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="ui-input @error('orden') is-invalid @enderror" value="{{ old('orden', 0) }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="activo" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(245,158,11,.05);">
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
