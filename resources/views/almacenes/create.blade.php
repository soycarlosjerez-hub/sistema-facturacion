@extends('layouts.app')
@section('title', 'Crear Almacén')

@push('styles')
<style>
    .premium-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 1rem; padding: 2rem; color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
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
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="premium-header d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="bi bi-buildings me-3 fs-1 opacity-75"></i>Nuevo Almacén
                    </h2>
                    <p class="mb-0 opacity-75 fs-5">Registra un nuevo almacén en el sistema</p>
                </div>
                <div>
                    <a href="{{ route('almacenes.index') }}" class="btn btn-light text-white bg-white bg-opacity-25 border-0 rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>

            {{-- Session error --}}
            @if (session('error'))
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form Card --}}
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom border-light p-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-building me-2 text-primary"></i>Información del Almacén</h5>
                </div>

                <form action="{{ route('almacenes.store') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">
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

                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('almacenes.index') }}" class="btn btn-light rounded-pill px-4 fw-semibold me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                            <i class="bi bi-check-lg me-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
