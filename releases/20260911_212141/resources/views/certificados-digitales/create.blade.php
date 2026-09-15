@extends('layouts.app')
@section('title', 'Nuevo Certificado Digital')

@push('styles')
@include('partials.premium-ui')
@endpush

@section('content')
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s;background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Certificado Digital</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span>Sube tu archivo .p12 o .pfx para firmar e-CF</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="{{ route('certificados-digitales.index') }}" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
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
                        <label class="ui-label">Nombre del Certificado</label>
                        <input type="text" name="nombre" class="ui-input" placeholder="Ej: Certificado Producción 2026" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Entidad Emisora</label>
                        <input type="text" name="emisor_cert" class="ui-input" placeholder="Ej: Certec, Digicert, AlS firmas">
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">RNC del Emisor (de la entidad)</label>
                        <input type="text" name="rnc_emisor" class="ui-input" placeholder="000000000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">RNC del Titular (su empresa)</label>
                        <input type="text" name="rnc_titular" class="ui-input" placeholder="000000000" required>
                    </div>

                    <div class="col-md-12">
                        <label class="ui-label">Archivo del Certificado (.p12 o .pfx)</label>
                        <input type="file" name="archivo" class="ui-input" accept=".p12,.pfx" required>
                        <small class="text-muted">Tamaño máximo: 2MB</small>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Contraseña del Certificado</label>
                        <input type="password" name="password" class="ui-input" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Número de Serie (opcional)</label>
                        <input type="text" name="serial_number" class="ui-input">
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" class="ui-input">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input" required>
                    </div>

                    <div class="col-md-12">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-input" rows="2" placeholder="Información adicional..."></textarea>
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
    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo certificado digital</span>
        </div>
        <div>
            <a href="{{ route('certificados-digitales.index') }}" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="certificadoForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Certificado
            </button>
        </div>
    </div>
</div>
@endsection
