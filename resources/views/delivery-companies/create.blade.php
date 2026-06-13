@extends('layouts.app')

@section('title', 'Nueva Empresa de Delivery')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            @if ($errors->any())
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="bi bi-truck text-warning me-2"></i>
                        Nueva Empresa de Delivery
                    </h2>
                    <p class="text-muted mb-0">Registra una plataforma de delivery externa</p>
                </div>
                <a href="{{ route('delivery-companies.index') }}" class="btn btn-light rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form method="POST" action="{{ route('delivery-companies.store') }}">
                    @csrf
                    <div class="card-header bg-light border-bottom p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-truck me-2"></i>Información de la Empresa</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="nombre" class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" required maxlength="100" placeholder="Ej: Uber Eats">
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="nombre_corto" class="form-label small fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_corto" id="nombre_corto" class="form-control @error('nombre_corto') is-invalid @enderror"
                                   value="{{ old('nombre_corto') }}" required maxlength="30" placeholder="Ej: uber_eats">
                            @error('nombre_corto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Identificador único usado internamente (snake_case).</div>
                        </div>
                        <div class="mb-3">
                            <label for="comision_porcentaje" class="form-label small fw-semibold">Comisión (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" max="100" name="comision_porcentaje" id="comision_porcentaje"
                                       class="form-control @error('comision_porcentaje') is-invalid @enderror"
                                       value="{{ old('comision_porcentaje') }}" required>
                                <span class="input-group-text">%</span>
                                @error('comision_porcentaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" checked>
                            <label for="activo" class="form-check-label small fw-semibold">Activo</label>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-top p-4 text-end">
                        <a href="{{ route('delivery-companies.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-check-lg me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
