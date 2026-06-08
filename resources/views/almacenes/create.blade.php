@extends('layouts.app')
@section('title', 'Crear Almacén')
@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-building text-primary me-2"></i>Crear Almacén</h2>
                    <p class="text-muted mb-0">Registra un nuevo almacén en el sistema</p>
                </div>
                <a href="{{ route('almacenes.index') }}" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
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
