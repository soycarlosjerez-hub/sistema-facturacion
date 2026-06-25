@extends('layouts.app')

@section('title', 'Nueva Sucursal')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .sucursales-form-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .sucursales-form-card .form-control {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
body.dark-mode .sucursales-form-card .form-label { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nueva Sucursal</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-geo-alt me-1"></i>
                        Registra una nueva sucursal en el sistema
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                    <i class="bi bi-arrow-left me-1"></i>Volver
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

    <div class="premium-card sucursales-form-card" style="animation-delay:.1s;">
        <div class="card-accent purple"></div>
        <div class="premium-card-title">
            <i class="bi bi-building icon-purple"></i>
            Información de la Sucursal
        </div>
        <div class="card-body">
            <form action="{{ route('sucursales.store') }}" method="POST" id="instanceForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">C&oacute;digo <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}" required maxlength="20" placeholder="SUC-001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required maxlength="255" placeholder="Sucursal Principal">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Direcci&oacute;n</label>
                        <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" maxlength="500" placeholder="Calle, n&uacute;mero, sector, ciudad">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="50" placeholder="809-555-0101">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" maxlength="255" placeholder="sucursal@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">RNC</label>
                        <input type="text" name="rnc" class="form-control" value="{{ old('rnc') }}" maxlength="20" placeholder="123456789">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="es_matriz" name="es_matriz" value="1" {{ old('es_matriz') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="es_matriz">Es Matriz</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="activa">Activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Creando nueva sucursal
        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="{{ route('sucursales.index') }}" class="btn-cancel">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn-save">
                <i class="bi bi-save me-2"></i>Guardar Sucursal
            </button>
        </div>
    </div>
</div>
@endsection
