@extends('layouts.app')

@section('title', 'Editar Sucursal')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        position: relative; overflow: hidden;
    }
    .premium-header::after {
        content: ''; position: absolute; top: -50%; right: -20%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }
    .filter-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    .status-badge {
        padding: 0.4em 0.8em; border-radius: 2rem;
        font-weight: 500; font-size: 0.75rem; letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
            <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="bi bi-geo-alt me-3 fs-1 opacity-75"></i>Editar Sucursal
                    </h2>
                    <p class="mb-0 opacity-75 fs-5">Modifica los datos de la sucursal</p>
                </div>
                <div>
                    <a href="{{ route('sucursales.index') }}" class="btn btn-light text-white bg-white bg-opacity-25 border-0 rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
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
@endsection
