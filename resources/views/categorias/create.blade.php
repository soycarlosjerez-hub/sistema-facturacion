@extends('layouts.app')
@section('title', 'Nueva Categoría')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .premium-card .form-check-input:checked {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Categoría</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>Agrega una nueva clasificación para productos
                    </small>
                </div>
            </div>
            <a href="{{ route('categorias.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
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
        <div class="premium-card" style="animation-delay:.1s;">
            <div class="card-accent purple"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #8b5cf6;">
                        <i class="bi bi-info-circle me-2"></i>Información de la Categoría
                    </h6>
                    <small class="text-muted">Datos básicos de la categoría</small>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Alimentos, Bebidas, Limpieza">
                        @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3" placeholder="Descripción opcional">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#8b5cf6;">
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

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva categoría</span>
        </div>
        <div>
            <a href="{{ route('categorias.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="categoriaForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Categoría
            </button>
        </div>
    </div>
</div>
@endsection
