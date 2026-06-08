@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-pencil-square text-warning me-2"></i>
                Editar Cliente
            </h2>
            <p class="text-muted mb-0">{{ $cliente->nombre }}</p>
        </div>
        <div>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary rounded-pill me-2">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </a>
            <button type="submit" form="form-cliente" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-save me-1"></i> Actualizar
            </button>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form-cliente" action="{{ route('clientes.update', $cliente) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">RNC / Cédula</label>
                                <input type="text" name="rnc_cedula" class="form-control" maxlength="11" placeholder="RNC o Cédula" value="{{ old('rnc_cedula', $cliente->rnc_cedula ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Dirección</label>
                                <textarea name="direccion" class="form-control" rows="3">{{ old('direccion', $cliente->direccion) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>
                        Información
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Los campos marcados con * son obligatorios.
                    </p>
                    <hr>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Cliente registrado {{ $cliente->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="card-footer bg-white border-0">
                    <button type="submit" form="form-cliente" class="btn btn-primary w-100 rounded-pill py-2">
                        <i class="bi bi-save me-1"></i> Actualizar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
