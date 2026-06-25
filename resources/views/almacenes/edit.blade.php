@extends('layouts.app')

@section('title', 'Editar Almacén')

@push('styles')
@include('partials.premium-ui')
<style>
    .btn-icon-hover {
        width: 32px; height: 32px;
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: background-color 0.2s;
    }
    .btn-icon-hover:hover { background-color: rgba(0,0,0,0.05); }
    body.dark-mode .btn-icon-hover:hover { background-color: rgba(255,255,255,0.1); }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3 premium-page">

    <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Editar Almacén</h2>
                <p class="mb-0 opacity-75 fs-5">Actualizar información del almacén</p>
            </div>
        </div>
        <div>
            <a href="{{ route('almacenes.index') }}" class="btn btn-light text-white bg-white bg-opacity-25 border-0 rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li><i class="bi bi-exclamation-triangle me-1"></i>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('almacenes.update', $almacen) }}" method="POST" id="instanceForm">
        @csrf
        @method('PUT')
        <div class="premium-card">
            <div class="card-accent blue"></div>
            <h5 class="premium-card-title"><i class="bi bi-building icon-blue"></i> Información del Almacén</h5>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label fw-semibold">Nombre del almacén <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ej: Almacén Principal" value="{{ old('nombre', $almacen->nombre) }}" autofocus required>
                    </div>

                    <div class="col-md-6">
                        <label for="ubicacion" class="form-label fw-semibold">Ubicación</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="form-control" placeholder="Ej: Santo Domingo" value="{{ old('ubicacion', $almacen->ubicacion) }}">
                    </div>

                    @if(isset($sucursales) && $sucursales->count())
                    <div class="col-12">
                        <label for="sucursal_id" class="form-label fw-semibold">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="form-select">
                            <option value="">Sin asignar</option>
                            @foreach($sucursales as $s)
                                <option value="{{ $s->id }}" {{ old('sucursal_id', $almacen->sucursal_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i> Editando almacén
            </span>
            <div class="d-flex gap-2 ms-auto">
                <a href="{{ route('almacenes.index') }}" class="btn btn-cancel rounded-pill px-4">Cancelar</a>
                <button type="submit" form="instanceForm" class="btn btn-save rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Guardar Almacén
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
