@extends('layouts.app')

@section('title', 'Configuración del Sistema')

@push('styles')
@include('partials.premium-ui')
<style>
body.dark-mode .ui-sticky-bar {
    border-top-color: #a78bfa !important;
}
body.dark-mode .ui-sticky-bar .ui-btn-solid {
    background: linear-gradient(135deg, #7c3aed, #6d28d9) !important;
}
</style>
@endpush

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0" role="alert" style="border-left: 4px solid #dc3545 !important;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;padding-bottom: 80px;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-gear"></i>
                </div>
                <div>
                    <h2 class="ui-header-title">Parámetros del Sistema</h2>
                    <div class="ui-header-meta">Personaliza la información de tu negocio y reglas de facturación</div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('configuracion.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card mb-4" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <h5 class="ui-card-title"><i class="bi bi-shop"></i>Información de Identidad</h5>
                    <p class="ui-card-subtitle">Datos principales del establecimiento</p>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label small fw-bold">Nombre del Establecimiento</label>
                                <input type="text" name="empresa_nombre" class="ui-input" value="{{ $settings['empresa_nombre'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label small fw-bold">RNC (Registro Nacional)</label>
                                <input type="text" name="empresa_rnc" class="ui-input" value="{{ $settings['empresa_rnc'] ?? '' }}">
                            </div>
                            <div class="col-md-12">
                                <label class="ui-label small fw-bold">Eslogan / Nota de Factura</label>
                                <input type="text" name="sistema_slogan" class="ui-input" value="{{ $settings['sistema_slogan'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label small fw-bold">Teléfono de Contacto</label>
                                <input type="text" name="empresa_telefono" class="ui-input" value="{{ $settings['empresa_telefono'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label small fw-bold">Dirección Física</label>
                                <input type="text" name="empresa_direccion" class="ui-input" value="{{ $settings['empresa_direccion'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <h5 class="ui-card-title"><i class="bi bi-gear"></i>Preferencias de Sistema</h5>
                    <p class="ui-card-subtitle">Configuración general del sistema de facturación</p>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label small fw-bold">Impuesto Predeterminado (ITBIS %)</label>
                                <div class="ui-input-group">
                                    <span class="ui-input-group-text">%</span>
                                    <input type="number" step="0.01" name="impuesto_itbis" class="ui-input" value="{{ $settings['impuesto_itbis'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="ui-label small fw-bold">Eslogan / Nota de Factura</label>
                                <input type="text" name="sistema_slogan" class="ui-input" value="{{ $settings['sistema_slogan'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card mb-4" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body">
                        <h5 class="fw-bold mb-3">Guardar Cambios</h5>
                        <p class="small opacity-75 mb-4">Asegúrate de revisar todos los campos. Estos cambios afectarán la impresión de facturas y los reportes fiscales.</p>
                        <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill fw-bold py-2 shadow">
                            <i class="bi bi-save me-2"></i> Actualizar Sistema
                        </button>
                    </div>
                </div>

                <div class="ui-card" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3">Respaldo</h6>
                        <p class="text-muted small mb-3">Se recomienda realizar un respaldo de la base de datos antes de cambiar parámetros críticos.</p>
                        <button type="button" class="ui-btn ui-btn-ghost btn-sm w-100 rounded-pill" disabled>
                            Descargar Backup (Próximamente)
                        </button>
                    </div>
                </div>

                <div class="ui-card mt-3" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-magic me-2"></i>Configuración Inicial</h6>
                        <p class="text-muted small mb-3">
                            Completa los pasos para tener todo listo y empezar a facturar en esta instancia.
                        </p>
                        @if(!$businessInstance->setup_completed)
                            <div class="alert alert-warning rounded-3 p-2 small mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Setup pendiente</span>
                            </div>
                        @endif
                        <a href="{{ route('setup.wizard') }}" class="ui-btn ui-btn-ghost w-100 rounded-pill fw-bold">
                            <i class="bi bi-arrow-right-circle me-1"></i> Abrir Wizard
                        </a>
                        @if($businessInstance->setup_completed)
                            <div class="mt-2 small text-success text-center">
                                <i class="bi bi-check-circle"></i> Completado
                            </div>
                            <div class="mt-2 text-center">
                                <a href="{{ route('setup.restart') }}" class="text-muted small"
                                   onclick="confirmAction({title:'Reiniciar Configuración', text:'¿Reiniciar la configuración? Se perderán todos los ajustes personalizados.', icon:'warning', color:'#f59e0b', confirmText:'Reiniciar', onSubmit:function(){ var f=document.createElement('form');f.method='POST';f.action=this.getAttribute('href');f.innerHTML='@csrf';document.body.appendChild(f);f.submit(); }})">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-12">
                <div id="correo-smtp" class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <h5 class="ui-card-title"><i class="bi bi-envelope-at"></i>Correo Electrónico (SMTP)</h5>
                    <p class="ui-card-subtitle">Configuración del servidor de correo saliente</p>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Controlador</label>
                                <select name="mail_mailer" class="ui-select">
                                    <option value="log" {{ ($settings['mail_mailer'] ?? 'log') === 'log' ? 'selected' : '' }}>Log (solo depuración)</option>
                                    <option value="smtp" {{ ($settings['mail_mailer'] ?? '') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($settings['mail_mailer'] ?? '') === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Host SMTP</label>
                                <input type="text" name="mail_host" class="ui-input" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small fw-bold">Puerto</label>
                                <input type="number" name="mail_port" class="ui-input" value="{{ $settings['mail_port'] ?? '587' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small fw-bold">Cifrado</label>
                                <select name="mail_encryption" class="ui-select">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($settings['mail_encryption'] ?? '') === 'null' ? 'selected' : '' }}>Ninguno</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Usuario</label>
                                <input type="text" name="mail_username" class="ui-input" value="{{ $settings['mail_username'] ?? '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Contraseña</label>
                                <input type="password" name="mail_password" class="ui-input" placeholder="{{ ($settings['mail_password'] ?? '') ? '********' : 'Ingresar contraseña' }}">
                                <div class="form-text">Dejar vacío para conservar la contraseña actual.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Dirección Remitente</label>
                                <input type="email" name="mail_from_address" class="ui-input" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="no-reply@ejemplo.com">
                            </div>
                            <div class="col-md-4">
                                <label class="ui-label small fw-bold">Nombre Remitente</label>
                                <input type="text" name="mail_from_name" class="ui-input" value="{{ $settings['mail_from_name'] ?? '' }}" placeholder="Sistema de Facturación">
                            </div>
                        </div>

                        <hr class="my-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="small text-muted mb-md-0">Guarda los cambios primero antes de probar. El correo de prueba usará la configuración SMTP guardada.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-toggle="modal" data-bs-target="#testEmailModal">
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
                    <label class="ui-label small fw-bold">Correo Destinatario</label>
                    <input type="email" name="test_email" class="ui-input" required placeholder="correo@ejemplo.com">
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="ui-btn ui-btn-ghost rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill px-4">
                        <i class="bi bi-send me-1"></i> Enviar Prueba
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sticky Bottom Save Bar -->
<div id="stickySaveBar" class="ui-sticky-bar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2" id="saveBarLeft">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Configuración del Sistema</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="configForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
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