@extends('layouts.app')

@section('title', 'Editar Cliente')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
    position: relative;
    overflow: hidden;
}
.premium-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
}
.sticky-save-bar {
    position: fixed;
    bottom: 0;
    left: var(--sidebar-width, 280px);
    right: 0;
    background: #fff;
    border-top: 2px solid #10b981;
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
    border-top-color: #34d399;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 pb-5">

    <div class="premium-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-pencil-square me-2"></i>
                    Editar Cliente
                </h3>
                <p class="mb-0 opacity-75">{{ $cliente->nombre }}</p>
            </div>
            <a href="{{ route('clientes.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Volver
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
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(10px);">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <form id="clienteForm" action="{{ route('clientes.update', $cliente) }}" method="POST">
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
            </div>
        </div>
    </div>

    <div class="sticky-save-bar">
        <div class="d-flex justify-content-end align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i>
                Editando: {{ $cliente->nombre }}
            </span>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-x-lg me-1"></i> Cancelar
            </a>
            <button type="submit" form="clienteForm" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Actualizar Cliente
            </button>
        </div>
    </div>

</div>
@endsection
