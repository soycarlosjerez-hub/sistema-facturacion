@extends('layouts.app')
@section('title', 'Nueva Categoría')

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Categoría</h4>
                    <div class="ui-header-meta">Agrega una nueva clasificación para productos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('categorias.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="categoriaForm" action="{{ route('categorias.store') }}" method="POST">
        @csrf
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-info-circle me-2"></i>Información de la Categoría
                    </h6>
                    <small class="text-muted">Datos básicos de la categoría</small>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Alimentos, Bebidas, Limpieza">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" class="ui-textarea @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-gear me-2"></i>Estado
                    </h6>
                    <small class="text-muted">Configuración de visibilidad</small>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" checked role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            <label class="form-check-label fw-semibold ms-2" for="activa" style="cursor: pointer;">
                                <i class="bi bi-check-circle text-success me-1"></i>Categoría activa
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1 ms-1">Si está activa, los productos podrán asignarse a esta categoría.</small>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('categorias.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="categoriaForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Categoría
        </button>
    </div>
</div>
@endsection
