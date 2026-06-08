@extends('layouts.app')

@section('title', 'Nueva Sucursal')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-building-add text-primary me-2"></i>Nueva Sucursal</h2>
                    <p class="text-muted mb-0">Registra un nuevo punto de venta o sucursal.</p>
                </div>
                <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
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

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4"><h5 class="fw-bold mb-0 text-dark"><i class="bi bi-building me-2 text-primary"></i>Información de la Sucursal</h5></div>
                <div class="card-body p-4">
                    <form action="{{ route('sucursales.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">C&oacute;digo <span class="text-danger">*</span></label>
                                <input type="text" name="codigo" class="form-control form-control-lg" value="{{ old('codigo') }}" required maxlength="20" placeholder="SUC-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre') }}" required maxlength="255" placeholder="Sucursal Principal">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Direcci&oacute;n</label>
                                <input type="text" name="direccion" class="form-control form-control-lg" value="{{ old('direccion') }}" maxlength="500" placeholder="Calle, n&uacute;mero, sector, ciudad">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tel&eacute;fono</label>
                                <input type="text" name="telefono" class="form-control form-control-lg" value="{{ old('telefono') }}" maxlength="50" placeholder="809-555-0101">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email') }}" maxlength="255" placeholder="sucursal@ejemplo.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">RNC</label>
                                <input type="text" name="rnc" class="form-control form-control-lg" value="{{ old('rnc') }}" maxlength="20" placeholder="123456789">
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
                <div class="card-footer bg-light border-top border-light p-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
