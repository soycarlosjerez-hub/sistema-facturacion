@extends('layouts.app')

@section('title', 'Editar Sucursal')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-building-gear text-primary me-2"></i>Editar Sucursal</h2>
                    <p class="text-muted mb-0">{{ $sucursal->nombre }} ({{ $sucursal->codigo }})</p>
                </div>
                <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('sucursales.update', $sucursal) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">C&oacute;digo <span class="text-danger">*</span></label>
                                <input type="text" name="codigo" class="form-control form-control-lg" value="{{ old('codigo', $sucursal->codigo) }}" required maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" class="form-control form-control-lg" value="{{ old('nombre', $sucursal->nombre) }}" required maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Direcci&oacute;n</label>
                                <input type="text" name="direccion" class="form-control form-control-lg" value="{{ old('direccion', $sucursal->direccion) }}" maxlength="500">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tel&eacute;fono</label>
                                <input type="text" name="telefono" class="form-control form-control-lg" value="{{ old('telefono', $sucursal->telefono) }}" maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control form-control-lg" value="{{ old('email', $sucursal->email) }}" maxlength="255">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">RNC</label>
                                <input type="text" name="rnc" class="form-control form-control-lg" value="{{ old('rnc', $sucursal->rnc) }}" maxlength="20">
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" role="switch" id="es_matriz" name="es_matriz" value="1" {{ old('es_matriz', $sucursal->es_matriz) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="es_matriz">Es Matriz</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" role="switch" id="activa" name="activa" value="1" {{ old('activa', $sucursal->activa) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="activa">Activa</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('sucursales.index') }}" class="btn btn-light rounded-pill px-4">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-save me-2"></i>Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
