@extends('layouts.app')
@section('title', 'Crear Almacén')

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
<div class="container-fluid px-4 premium-page">

    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex flex-wrap justify-content-between align-items-center position-relative" style="z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Almacén</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra un nuevo almacén en el sistema
                    </small>
                </div>
            </div>
            <div>
                <a href="{{ route('almacenes.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="premium-card">
        <div class="card-accent blue"></div>
        <h5 class="premium-card-title"><i class="bi bi-building icon-blue"></i> Información del Almacén</h5>

        <form action="{{ route('almacenes.store') }}" method="POST" id="instanceForm">
            @csrf
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label fw-semibold">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Ubicación</label>
                    <input type="text" name="ubicacion" class="form-control">
                </div>
                @if(isset($sucursales) && $sucursales->count())
                <div class="mb-4">
                    <label class="form-label fw-semibold">Sucursal</label>
                    <select name="sucursal_id" class="form-select">
                        <option value="">Sin asignar</option>
                        @foreach($sucursales as $s)
                            <option value="{{ $s->id }}" {{ old('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#3b82f6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo almacén</span>
        </div>
        <div>
            <a href="{{ route('almacenes.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Almacén
            </button>
        </div>
    </div>
</div>
@endsection
