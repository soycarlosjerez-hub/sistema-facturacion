@extends('layouts.app')
@section('title', 'Editar Certificado')

@push('styles')
<style>
.premium-header {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    border-radius: 1rem;
    padding: 2rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);
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
    border-top: 2px solid #0ea5e9;
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
    border-top-color: #38bdf8;
}
@media (max-width: 991.98px) {
    .sticky-save-bar { left: 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="premium-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-key me-2"></i>Editar Certificado</h3>
            <p class="mb-0 opacity-75">{{ $cert->nombre }}</p>
        </div>
        <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-pill text-dark fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
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

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-5" style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px);">
        <form id="certificadoForm" action="{{ route('certificados-digitales.update', $cert) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #0ea5e9;">
                        <i class="bi bi-info-circle me-2"></i>Información del Certificado
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre</label>
                        <input type="text" name="nombre" class="form-control rounded-3" value="{{ $cert->nombre }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Entidad Emisora</label>
                        <input type="text" name="emisor_cert" class="form-control rounded-3" value="{{ $cert->emisor_cert }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">RNC Emisor</label>
                        <input type="text" name="rnc_emisor" class="form-control rounded-3" value="{{ $cert->rnc_emisor }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">RNC Titular</label>
                        <input type="text" name="rnc_titular" class="form-control rounded-3" value="{{ $cert->rnc_titular }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Reemplazar Archivo (opcional)</label>
                        <input type="file" name="archivo" class="form-control rounded-3" accept=".p12,.pfx">
                        <small class="text-muted">Solo suba un nuevo archivo si desea reemplazar el actual</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nueva Contraseña (opcional)</label>
                        <input type="password" name="password" class="form-control rounded-3" placeholder="Solo si la cambió">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Número de Serie</label>
                        <input type="text" name="serial_number" class="form-control rounded-3" value="{{ $cert->serial_number }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" class="form-control rounded-3" value="{{ $cert->fecha_emision?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control rounded-3" value="{{ $cert->fecha_vencimiento->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2">{{ $cert->notas }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" {{ $cert->activo ? 'checked' : '' }}>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="sticky-save-bar d-flex justify-content-between align-items-center">
    <div>
        <span class="fw-semibold" style="color: #0ea5e9;"><i class="bi bi-key me-1"></i> Editando: {{ $cert->nombre }}</span>
    </div>
    <button type="submit" form="certificadoForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
        <i class="bi bi-check-circle me-1"></i> Actualizar Certificado
    </button>
</div>
@endsection
