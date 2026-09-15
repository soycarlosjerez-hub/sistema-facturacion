@extends('layouts.app')
@section('title', 'Crear Almacén')

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
                    <h4 class="ui-header-title">Nuevo Almacén</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Registra un nuevo almacén en el sistema</span>
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

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h5 class="fw-bold mb-4"><i class="bi bi-building me-2" style="color:var(--accent);"></i> Información del Almacén</h5>

            <form action="{{ route('almacenes.store') }}" method="POST" id="instanceForm">
                @csrf
                <div class="mb-4">
                    <label class="ui-label">Nombre</label>
                    <input type="text" name="nombre" class="ui-input" required>
                </div>
                <div class="mb-4">
                    <label class="ui-label">Ubicación</label>
                    <input type="text" name="ubicacion" class="ui-input">
                </div>
                @if(isset($sucursales) && $sucursales->count())
                <div class="mb-4">
                    <label class="ui-label">Sucursal</label>
                    <select name="sucursal_id" class="ui-select">
                        <option value="">Sin asignar</option>
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="{{ route('almacenes.index') }}" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Almacén
        </button>
    </div>
</div>
@endsection
