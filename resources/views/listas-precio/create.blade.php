@extends('layouts.app')
@section('title', 'Nueva Lista de Precios')

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
            <div class="premium-header mb-4">
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="premium-avatar-circle">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-white">Nueva Lista de Precios</h4>
                            <small class="text-white opacity-75">
                                <i class="bi bi-plus-circle me-1"></i>
                                Define una nueva lista con precios especiales
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('listas-precio.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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
            <form id="listaPrecioForm" action="{{ route('listas-precio.store') }}" method="POST">
                @csrf
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4 pb-3 border-bottom">
                        <h6 class="fw-bold mb-0" style="color: #8b5cf6;">
                            <i class="bi bi-info-circle me-2"></i>Información de la Lista
                        </h6>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">C&oacute;digo <span class="text-danger">*</span></label>
                            <input type="text" name="codigo" class="form-control form-control-lg" value="{{ old('codigo') }}" required maxlength="20" placeholder="MAYORISTA">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}" required maxlength="255" placeholder="Precio Mayorista">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Descripci&oacute;n</label>
                            <textarea name="descripcion" class="form-control" rows="2" placeholder="Opcional: descripci&oacute;n de la lista">{{ old('descripcion') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Vigencia desde</label>
                            <input type="date" name="vigencia_desde" class="form-control" value="{{ old('vigencia_desde') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Vigencia hasta</label>
                            <input type="date" name="vigencia_hasta" class="form-control" value="{{ old('vigencia_hasta') }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="activa">Activa</label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color: #8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva lista de precios</span>
        </div>
        <div>
            <a href="{{ route('listas-precio.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="listaPrecioForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Lista
            </button>
        </div>
    </div>
</div>
@endsection