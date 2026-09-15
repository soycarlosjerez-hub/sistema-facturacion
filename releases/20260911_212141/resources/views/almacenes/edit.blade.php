@extends('layouts.app')

@section('title', 'Editar Almacén')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page" style="--accent:#14b8a6;--accent-rgb:20,184,166;--accent-hover:#0d9488;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Almacén</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span>Actualizar información del almacén</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('almacenes.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
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
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="fw-bold mb-4"><i class="bi bi-building me-2" style="color:var(--accent);"></i> Información del Almacén</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="nombre" class="ui-label">Nombre del almacén <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="ui-input" placeholder="Ej: Almacén Principal" value="{{ old('nombre', $almacen->nombre) }}" autofocus required>
                    </div>

                    <div class="col-md-6">
                        <label for="ubicacion" class="ui-label">Ubicación</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="ui-input" placeholder="Ej: Santo Domingo" value="{{ old('ubicacion', $almacen->ubicacion) }}">
                    </div>

                    @if(isset($sucursales) && $sucursales->count())
                    <div class="col-12">
                        <label for="sucursal_id" class="ui-label">Sucursal</label>
                        <select name="sucursal_id" id="sucursal_id" class="ui-select">
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

    <div class="ui-sticky-bar">
        <div class="ui-sticky-bar-inner">
            <a href="{{ route('almacenes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-save me-2"></i>Guardar Almacén
            </button>
        </div>
    </div>
</div>
@endsection
