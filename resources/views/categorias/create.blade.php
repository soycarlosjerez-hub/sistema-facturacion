@extends('layouts.app')
@section('title', 'Nueva Categoría')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.4);
        position: relative;
        overflow: hidden;
    }
    .premium-header::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #ec4899;
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .sticky-save-bar .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #f472b6;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white bg-opacity-20 rounded-2 p-2 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                    <i class="bi bi-tags fs-2 text-white"></i>
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

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <form id="categoriaForm" action="{{ route('categorias.store') }}" method="POST">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #ec4899;">
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

<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva categoría</span>
        </div>
        <button type="submit" form="categoriaForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-save me-1"></i>Guardar Categoría
        </button>
    </div>
</div>
@endsection
