@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid px-4" style="padding-bottom: 80px;">
    <!-- Header Moderno -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0">Parámetros del Sistema</h3>
            <p class="text-muted mb-0">Personaliza la información de tu negocio y reglas de facturación</p>
        </div>
    </div>

    <form action="{{ route('configuracion.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Datos de la Empresa -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header border-0 bg-transparent py-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-shop me-2"></i>Información de Identidad</h6>
                    </div>
                    <div class="card-body p-4">
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

                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-0 bg-transparent py-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-gear me-2"></i>Preferencias de Sistema</h6>
                    </div>
                    <div class="card-body p-4">
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

            <!-- Panel de Acciones Laterales -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">Guardar Cambios</h5>
                        <p class="small opacity-75 mb-4">Asegúrate de revisar todos los campos. Estos cambios afectarán la impresión de facturas y los reportes fiscales.</p>
                        <button type="submit" class="btn btn-white w-100 rounded-pill fw-bold py-2 shadow">
                            <i class="bi bi-save me-2"></i> Actualizar Sistema
                        </button>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4">
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

        <!-- Configuración SMTP -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div id="correo-smtp" class="card border-0 shadow-sm rounded-4">
                    <div class="card-header border-0 bg-transparent py-3">
                        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-envelope-at me-2"></i>Correo Electrónico (SMTP)</h6>
                    </div>
                    <div class="card-body p-4">
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
                                <input type="password" name="mail_password" class="form-control rounded-3" placeholder="{{ $settings['mail_password'] ? '********' : 'Ingresar contraseña' }}">
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

<!-- Sticky Bottom Save Bar - Always Visible -->
<div id="stickySaveBar" class="sticky-save-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" id="saveBarLeft">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Configuración del Sistema</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="configForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>

<style>
    .btn-white {
        background: white;
        color: var(--bs-primary);
        border: none;
    }
    .btn-white:hover {
        background: #f8fafc;
        color: var(--bs-primary-dark);
    }
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 0px);
        right: 0;
        background: #fff;
        border-top: 2px solid var(--bs-primary, #0d6efd);
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #38bdf8;
    }
    @media (max-width: 991.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>

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
