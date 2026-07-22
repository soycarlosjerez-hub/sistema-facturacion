@extends('layouts.app')

@section('title', 'Nueva Caja')

@push('styles')
@include('partials.premium-ui')
<style>
    .premium-header {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 8px 32px rgba(245,158,11,.25);
    }
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
                            <i class="bi bi-cash-register"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1 text-white">Nueva Caja</h4>
                            <small class="text-white opacity-75">
                                <i class="bi bi-plus-circle me-1"></i>
                                Registra una nueva caja registradora en el sistema
                            </small>
                        </div>
                    </div>
                    <a href="{{ route('cajas.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                        <i class="bi bi-arrow-left me-1"></i>Volver
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
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cajas.store') }}" method="POST" id="instanceForm">
                @csrf
                <div class="premium-card">
                    <div class="card-accent amber"></div>
                    <div class="premium-card-title">
                        <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
                        Información de la Caja
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" required placeholder="Ej. Caja 1, Caja Express, Mostrador 2" value="{{ old('nombre') }}">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Código</label>
                                <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror" placeholder="C01, C02..." value="{{ old('codigo', $nextCode) }}">
                                <small class="text-muted">Identificador corto único.</small>
                                @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Ubicación</label>
                                <input type="text" name="ubicacion" class="form-control" placeholder="Ej. Mostrador principal, Segundo piso" value="{{ old('ubicacion') }}">
                            </div>

                            @if(isset($sucursales) && $sucursales->count())
                            <div class="col-12">
                                <label class="form-label fw-semibold">Sucursal</label>
                                <select name="sucursal_id" class="form-select">
                                    <option value="">Sin asignar</option>
                                    @foreach($sucursales as $s)
                                        <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ old('activo', true) ? 'checked' : '' }}>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold mb-0" for="activo">Caja activa</label>
                                        <small class="d-block text-muted">Las cajas inactivas no pueden abrir turnos. Puedes cambiar esto después.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva caja</span>
        </div>
        <div>
            <a href="{{ route('cajas.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Caja
            </button>
        </div>
    </div>
</div>
@endsection
