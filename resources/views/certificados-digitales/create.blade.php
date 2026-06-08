@extends('layouts.app')

@section('title', 'Nuevo Certificado Digital')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-key text-primary me-2"></i>Nuevo Certificado Digital</h2>
                    <p class="text-muted mb-0">Sube tu archivo .p12 o .pfx para firmar e-CF</p>
                </div>
                <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <form action="{{ route('certificados-digitales.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-header bg-light border-bottom border-light p-4">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-key me-2"></i>Información del Certificado</h5>
                    </div>
                    <div class="card-body p-4">
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
                    <div class="card-footer bg-light border-top border-light p-4 text-end">
                        <a href="{{ route('certificados-digitales.index') }}" class="btn btn-light rounded-pill px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            <i class="bi bi-save me-1"></i>Guardar Certificado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
