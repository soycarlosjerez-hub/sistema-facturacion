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
<div class="premium-page">
    <div class="container-fluid px-4">
        <div class="premium-header">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    <div class="premium-avatar-circle">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div>
                        <h2 class="fw-bold mb-0 text-white">Nueva Categoría</h2>
                        <p class="text-white text-opacity-75 mb-0">Agrega una nueva clasificación para productos</p>
                    </div>
                </div>
                <a href="{{ route('categorias.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
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

        <div class="premium-card mb-5">
            <div class="card-accent purple"></div>
            <form id="categoriaForm" action="{{ route('categorias.store') }}" method="POST">
                @csrf
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0" style="color: #8b5cf6;">
                            <i class="bi bi-info-circle me-2"></i>Información de la Categoría
                        </h6>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required placeholder="Ej. Alimentos, Bebidas, Limpieza">
                                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Descripción</label>
                                <textarea name="descripcion" class="form-control form-control-lg" rows="3" placeholder="Descripción opcional de la categoría">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" checked role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            <label class="form-check-label fw-semibold ms-2" for="activa" style="cursor: pointer;">
                                <i class="bi bi-check-circle text-success me-1"></i>Categoría activa
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1 ms-1">Si está activa, los productos podrán asignarse a esta categoría.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color: #8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva categoría</span>
        </div>
        <button type="submit" form="categoriaForm" class="btn-save">
            <i class="bi bi-save me-1"></i>Guardar Categoría
        </button>
    </div>
</div>
@endsection
