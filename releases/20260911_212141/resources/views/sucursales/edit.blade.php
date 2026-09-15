@extends('layouts.app')

@section('title', 'Editar Sucursal')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .sucursales-form-card {
    background: rgba(15,23,42,.8);
    border-color: rgba(255,255,255,.08);
}
body.dark-mode .sucursales-form-card .ui-input {
    background: rgba(15,23,42,.6);
    border-color: #334155;
    color: #f1f5f9;
}
body.dark-mode .sucursales-form-card .ui-label { color: #94a3b8; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-geo-alt"></i></div>
                <div>
                    <h4 class="ui-header-title">Editar Sucursal</h4>
                    <div class="ui-header-meta"><i class="bi bi-geo-alt me-1"></i> <span>Modifica los datos de la sucursal</span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('sucursales.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i>Volver</a>
            </div>
        </div>
    </div>

    <div class="ui-card sucursales-form-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body">
            <form action="{{ route('sucursales.update', $sucursal) }}" method="POST" id="instanceForm">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">C&oacute;digo <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="ui-input" value="{{ old('codigo', $sucursal->codigo) }}" required maxlength="20">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input" value="{{ old('nombre', $sucursal->nombre) }}" required maxlength="255">
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Direcci&oacute;n</label>
                        <input type="text" name="direccion" class="ui-input" value="{{ old('direccion', $sucursal->direccion) }}" maxlength="500">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="ui-input" value="{{ old('telefono', $sucursal->telefono) }}" maxlength="50">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Email</label>
                        <input type="email" name="email" class="ui-input" value="{{ old('email', $sucursal->email) }}" maxlength="255">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">RNC</label>
                        <input type="text" name="rnc" class="ui-input" value="{{ old('rnc', $sucursal->rnc) }}" maxlength="20">
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
            </form>
        </div>
    </div>

    <div class="premium-sticky-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i> Editando sucursal: {{ $sucursal->nombre }}
            </span>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('sucursales.index') }}" class="btn-cancel">Cancelar</a>
                <button type="submit" form="instanceForm" class="btn-save">
                    <i class="bi bi-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
