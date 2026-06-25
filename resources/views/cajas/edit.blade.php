@extends('layouts.app')

@section('title', 'Editar Caja')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
    }
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #f59e0b;
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
        border-top-color: #f59e0b;
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
                            <i class="bi bi-pencil-square fs-2 text-white"></i>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-0 text-white">Editar Caja</h2>
                            <p class="text-white text-opacity-75 mb-0">Modifica los datos de <strong class="text-white">{{ $caja->nombre }}</strong>.</p>
                        </div>
                    </div>
                    <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">No se pudo actualizar la caja</h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('cajas.update', $caja) }}" method="POST" id="instanceForm">
                @csrf @method('PUT')
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0"><i class="bi bi-cash-register text-primary me-2"></i>Editando: {{ $caja->nombre }}</h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">ID #{{ $caja->id }}</span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-tag-fill text-warning"></i></span>
                                    <input type="text" name="nombre" class="form-control border-start-0 @error('nombre') is-invalid @enderror" required value="{{ old('nombre', $caja->nombre) }}">
                                </div>
                                @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Código</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc text-warning"></i></span>
                                    <input type="text" name="codigo" class="form-control border-start-0 @error('codigo') is-invalid @enderror" value="{{ old('codigo', $caja->codigo) }}">
                                </div>
                                @error('codigo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Ubicación</label>
                                <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt-fill text-warning"></i></span>
                                    <input type="text" name="ubicacion" class="form-control border-start-0" value="{{ old('ubicacion', $caja->ubicacion) }}">
                                </div>
                            </div>

                            @if(isset($sucursales) && $sucursales->count())
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Sucursal</label>
                                <select name="sucursal_id" class="form-select form-select-lg shadow-sm rounded-3">
                                    <option value="">Sin asignar</option>
                                    @foreach($sucursales as $s)
                                        <option value="{{ $s->id }}" {{ old('sucursal_id', $caja->sucursal_id) == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3 {{ $caja->activo ? '' : '' }}" style="background: {{ $caja->activo ? 'rgba(34,197,94,0.08)' : 'rgba(239,68,68,0.08)' }}; border: 1px solid {{ $caja->activo ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)' }};">
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $caja->activo) ? 'checked' : '' }}>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold mb-0" for="activo">{{ $caja->activo ? 'Caja activa' : 'Caja inactiva' }}</label>
                                        <small class="d-block text-muted">
                                            @if($caja->estado == 'abierta')
                                                <i class="bi bi-exclamation-triangle text-warning"></i> Esta caja está abierta. Ciérrala antes de desactivarla.
                                            @else
                                                Las cajas inactivas no pueden abrir turnos.
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="col-12">
                                <div class="row g-2 small text-muted">
                                    <div class="col-md-4">
                                        <i class="bi bi-calendar-plus me-1"></i>Creada: <strong>{{ $caja->created_at->format('d/m/Y') }}</strong>
                                    </div>
                                    @if($caja->updated_at && $caja->updated_at != $caja->created_at)
                                    <div class="col-md-4">
                                        <i class="bi bi-pencil me-1"></i>Última edición: <strong>{{ $caja->updated_at->diffForHumans() }}</strong>
                                    </div>
                                    @endif
                                    <div class="col-md-4">
                                        <i class="bi bi-info-circle me-1"></i>Estado actual: <strong>{{ ucfirst($caja->estado) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
        </div>

        <div class="sticky-save-bar">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-info-circle me-1"></i> Editando caja: {{ $caja->nombre }}
                </span>
                <div class="d-flex gap-2 ms-auto">
                    <a href="{{ route('cajas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                    <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

</div>
@endsection
