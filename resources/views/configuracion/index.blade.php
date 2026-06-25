@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@push('styles')
@include('partials.premium-ui')
<style>
.premium-header {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6, #a855f7, #7c3aed) !important;
    background-size: 300% 300% !important;
    box-shadow: 0 8px 32px rgba(139,92,246,.25) !important;
}
.premium-card .form-control:focus,
.premium-card .form-select:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.15) !important;
}
.premium-card .btn-primary {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
    box-shadow: 0 4px 14px rgba(139,92,246,.3) !important;
}
.premium-card .btn-primary:hover {
    box-shadow: 0 6px 20px rgba(139,92,246,.45) !important;
}
.premium-sticky-bar {
    border-top-color: #8b5cf6 !important;
}
.premium-sticky-bar .btn-save {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
    box-shadow: 0 4px 14px rgba(139,92,246,.3) !important;
}
.premium-sticky-bar .btn-save:hover {
    box-shadow: 0 6px 20px rgba(139,92,246,.45) !important;
}
body.dark-mode .premium-sticky-bar {
    border-top-color: #a78bfa !important;
}
body.dark-mode .premium-sticky-bar .btn-save {
    background: linear-gradient(135deg, #7c3aed, #6d28d9) !important;
}
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus {
    border-color: #8b5cf6 !important;
}
</style>
@endpush

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container-fluid px-4 py-3 premium-page" style="padding-bottom: 80px;">
    <div class="premium-header mb-4" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6, #a855f7, #7c3aed) !important; background-size: 300% 300% !important;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex align-items-center gap-3">
            <div class="premium-avatar-circle">
                <i class="bi bi-gear"></i>
            </div>
            <div>
                <h2 class="fw-bold mb-1">Parámetros del Sistema</h2>
                <p class="text-white-50 mb-0">Personaliza la información de tu negocio y reglas de facturación</p>
            </div>
        </div>
    </div>

    <form action="{{ route('configuracion.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="premium-card mb-4">
                    <div class="card-accent purple"></div>
                    <h5 class="premium-card-title"><i class="bi bi-shop icon-purple"></i>Información de Identidad</h5>
                    <p class="premium-card-subtitle">Datos principales del establecimiento</p>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nombre del Establecimiento</label>
                                <input type="text" name="empresa_nombre" class="form-control rounded-3" value="{{ $settings['empresa_nombre'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">RNC (Registro Nacional)</label>
                                <input type="text" name="empresa_rnc" class="form-control rounded-3" value="{{ $settings['empresa_rnc'] ?? '' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Eslogan / Nota de Factura</label>
                                <input type="text" name="sistema_slogan" class="form-control rounded-3" value="{{ $settings['sistema_slogan'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Teléfono de Contacto</label>
                                <input type="text" name="empresa_telefono" class="form-control rounded-3" value="{{ $settings['empresa_telefono'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Dirección Física</label>
                                <input type="text" name="empresa_direccion" class="form-control rounded-3" value="{{ $settings['empresa_direccion'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <h5 class="premium-card-title"><i class="bi bi-gear icon-purple"></i>Preferencias de Sistema</h5>
                    <p class="premium-card-subtitle">Configuración general del sistema de facturación</p>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Impuesto Predeterminado (ITBIS %)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="impuesto_itbis" class="form-control rounded-start-3" value="{{ $settings['impuesto_itbis'] ?? '' }}">
                                    <span class="input-group-text bg-light">%</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Eslogan / Nota de Factura</label>
                                <input type="text" name="sistema_slogan" class="form-control rounded-3" value="{{ $settings['sistema_slogan'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="premium-card mb-4">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Guardar Cambios</h5>
                        <p class="small opacity-75 mb-4">Asegúrate de revisar todos los campos. Estos cambios afectarán la impresión de facturas y los reportes fiscales.</p>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow">
                            <i class="bi bi-save me-2"></i> Actualizar Sistema
                        </button>
                    </div>
                </div>

                <div class="premium-card">
                    <div class="card-accent purple"></div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Respaldo</h6>
                        <p class="text-muted small mb-3">Se recomienda realizar un respaldo de la base de datos antes de cambiar parámetros críticos.</p>
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 rounded-pill" disabled>
                            Descargar Backup (Próximamente)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-12">
                <div id="correo-smtp" class="premium-card">
                    <div class="card-accent purple"></div>
                    <h5 class="premium-card-title"><i class="bi bi-envelope-at icon-purple"></i>Correo Electrónico (SMTP)</h5>
                    <p class="premium-card-subtitle">Configuración del servidor de correo saliente</p>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Controlador</label>
                                <select name="mail_mailer" class="form-select rounded-3">
                                    <option value="log" {{ ($settings['mail_mailer'] ?? 'log') === 'log' ? 'selected' : '' }}>Log (solo depuración)</option>
                                    <option value="smtp" {{ ($settings['mail_mailer'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Host SMTP</label>
                                <input type="text" name="mail_host" class="form-control rounded-3" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Puerto</label>
                                <input type="number" name="mail_port" class="form-control rounded-3" value="{{ $settings['mail_port'] ?? '587' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Cifrado</label>
                                <select name="mail_encryption" class="form-select rounded-3">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($settings['mail_encryption'] ?? '') === 'null' ? 'selected' : '' }}>Ninguno</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Usuario</label>
                                <input type="text" name="mail_username" class="form-control rounded-3" value="{{ $settings['mail_username'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Contraseña</label>
                                <input type="password" name="mail_password" class="form-control rounded-3" placeholder="{{ ($settings['mail_password'] ?? '') ? '********' : 'Ingresar contraseña' }}">
                                <div class="form-text">Dejar vacío para conservar la contraseña actual.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Dirección Remitente</label>
                                <input type="email" name="mail_from_address" class="form-control rounded-3" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="no-reply@ejemplo.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Nombre Remitente</label>
                                <input type="text" name="mail_from_name" class="form-control rounded-3" value="{{ $settings['mail_from_name'] ?? '' }}" placeholder="Sistema de Facturación">
                            </div>
                        </div>

                        <hr class="my-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="small text-muted mb-md-0">Guarda los cambios primero antes de probar. El correo de prueba usará la configuración SMTP guardada.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button type="button" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                                    <i class="bi bi-send me-1"></i> Enviar Correo de Prueba
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Modal Test Email -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('configuracion.test-email') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h6 class="modal-title fw-bold">Enviar Correo de Prueba</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">Se enviará un correo de prueba usando la configuración SMTP actual.</p>
                    <label class="form-label small fw-bold">Correo Destinatario</label>
                    <input type="email" name="test_email" class="form-control rounded-3" required placeholder="correo@ejemplo.com">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-send me-1"></i> Enviar Prueba
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sticky Bottom Save Bar -->
<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" id="saveBarLeft">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Configuración del Sistema</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="configForm" class="btn-save rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    const form = document.querySelector('form[action*="configuracion"]');
    if (!form) return;
    form.id = 'configForm';

    const bar = document.getElementById('stickySaveBar');
    const leftEl = document.getElementById('saveBarLeft');
    const initial = new FormData(form).toString();

    form.addEventListener('input', function() {
        const current = new FormData(form).toString();
        if (current !== initial) {
            leftEl.innerHTML = '<i class="bi bi-exclamation-circle text-warning"></i><span class="fw-semibold d-none d-sm-inline">Tienes cambios sin guardar</span>';
        } else {
            leftEl.innerHTML = '<i class="bi bi-info-circle text-primary"></i><span class="fw-semibold d-none d-sm-inline">Configuración del Sistema</span>';
        }
    });
})();
</script>
@endsection
