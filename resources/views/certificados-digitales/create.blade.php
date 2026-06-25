@extends('layouts.app')
@section('title', 'Nuevo Certificado Digital')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .premium-sticky-bar { border-top-color: #f59e0b; }
body.dark-mode .premium-sticky-bar .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-sticky-bar .btn-save:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .alert-warning { background: rgba(245,158,11,.15); color: #fcd34d; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 premium-page">
    <div class="premium-header d-flex justify-content-between align-items-center mb-4" style="background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-key"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">Nuevo Certificado Digital</h3>
                <p class="mb-0 opacity-75">Sube tu archivo .p12 o .pfx para firmar e-CF</p>
            </div>
        </div>
        <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-pill text-dark fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
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

    <div class="premium-card mb-5">
        <div class="card-accent amber"></div>
        <form id="certificadoForm" action="{{ route('certificados-digitales.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información del Certificado
                    </h6>
                </div>
                <div class="alert alert-warning border-0 rounded-3 small mb-4">
                    <i class="bi bi-shield-lock me-1"></i>
                    El archivo del certificado se almacena de forma segura y la contraseña se cifra con la clave de la aplicación.
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombre del Certificado</label>
                        <input type="text" name="nombre" class="form-control rounded-3" placeholder="Ej: Certificado Producción 2026" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Entidad Emisora</label>
                        <input type="text" name="emisor_cert" class="form-control rounded-3" placeholder="Ej: Certec, Digicert, AlS firmas">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">RNC del Emisor (de la entidad)</label>
                        <input type="text" name="rnc_emisor" class="form-control rounded-3" placeholder="000000000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">RNC del Titular (su empresa)</label>
                        <input type="text" name="rnc_titular" class="form-control rounded-3" placeholder="000000000" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Archivo del Certificado (.p12 o .pfx)</label>
                        <input type="file" name="archivo" class="form-control rounded-3" accept=".p12,.pfx" required>
                        <small class="text-muted">Tamaño máximo: 2MB</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Contraseña del Certificado</label>
                        <input type="password" name="password" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Número de Serie (opcional)</label>
                        <input type="text" name="serial_number" class="form-control rounded-3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" class="form-control rounded-3">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control rounded-3" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2" placeholder="Información adicional..."></textarea>
                    </div>

                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" checked>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar d-flex justify-content-between align-items-center">
    <div>
        <span class="fw-semibold" style="color: #f59e0b;"><i class="bi bi-key me-1"></i> Creando nuevo certificado digital</span>
    </div>
    <button type="submit" form="certificadoForm" class="btn-save rounded-pill px-5 fw-bold shadow-sm">
        <i class="bi bi-check-circle me-1"></i> Guardar Certificado
    </button>
</div>
@endsection
