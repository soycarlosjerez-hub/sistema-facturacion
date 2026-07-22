@extends('layouts.app')

@section('title', 'Nueva Caja')

@push('styles')
@include('partials.premium-ui')
<style>
</style>
@endpush

@section('content')
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-register"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Caja</h4>
                    <div class="ui-header-meta">Registra una nueva caja registradora en el sistema</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('cajas.index') }}" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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
                        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cajas.store') }}" method="POST" id="instanceForm">
                @csrf
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Información de la Caja
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="ui-label fw-semibold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" class="ui-input @error('nombre') is-invalid @enderror" required placeholder="Ej. Caja 1, Caja Express, Mostrador 2" value="{{ old('nombre') }}">
                                @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-5">
                                <label class="ui-label fw-semibold">Código</label>
                                <input type="text" name="codigo" class="ui-input @error('codigo') is-invalid @enderror" placeholder="C01, C02..." value="{{ old('codigo', $nextCode) }}">
                                <small class="text-muted">Identificador corto único.</small>
                                @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="ui-label fw-semibold">Ubicación</label>
                                <input type="text" name="ubicacion" class="ui-input" placeholder="Ej. Mostrador principal, Segundo piso" value="{{ old('ubicacion') }}">
                            </div>

                            @if(isset($sucursales) && $sucursales->count())
                            <div class="col-12">
                                <label class="ui-label fw-semibold">Sucursal</label>
                                <select name="sucursal_id" class="ui-select">
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

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent);"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva caja</span>
        </div>
        <div>
            <a href="{{ route('cajas.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>Guardar Caja
            </button>
        </div>
    </div>
</div>
@endsection
