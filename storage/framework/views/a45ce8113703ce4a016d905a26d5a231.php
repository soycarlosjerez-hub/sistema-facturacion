<?php $__env->startSection('title', 'Configuración SMTP'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* ============================================================
   SMTP Settings — Custom Overrides
   ============================================================ */

/* Form helper text */
.form-helper {
    font-size: .78rem;
    color: #64748b;
    margin-top: .25rem;
    display: block;
}

/* Warning alert for error email */
.smtp-alert-box {
    background: linear-gradient(135deg, rgba(245,158,11,.08), rgba(245,158,11,.03));
    border: 1.5px solid rgba(245,158,11,.2);
    border-left: 4px solid #f59e0b;
    border-radius: var(--radius-lg);
    padding: 1.1rem 1.25rem;
}
.smtp-alert-box .alert-icon {
    font-size: 1.3rem;
    color: #d97706;
}

/* Info panel */
.smtp-info-panel {
    background: rgba(99,102,241,.04);
    border: 1px solid rgba(99,102,241,.1);
    border-radius: var(--radius);
    padding: 1rem 1.15rem;
}
.smtp-info-panel p {
    margin-bottom: .4rem;
    font-size: .84rem;
}
.smtp-info-panel p:last-child { margin-bottom: 0; }

/* Dark mode overrides */
body.dark-mode .form-helper { color: #64748b; }
body.dark-mode .smtp-alert-box {
    background: linear-gradient(135deg, rgba(245,158,11,.1), rgba(245,158,11,.03));
    border-color: rgba(245,158,11,.2);
}
body.dark-mode .smtp-alert-box .alert-icon { color: #fbbf24; }
body.dark-mode .smtp-info-panel {
    background: rgba(99,102,241,.06);
    border-color: rgba(99,102,241,.15);
}
body.dark-mode .smtp-info-panel p { color: #94a3b8; }
body.dark-mode hr { border-color: #1e293b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5">
<div class="container-fluid px-4 py-3">

    
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-envelope-at"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Configuración SMTP</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-gear me-1"></i>
                        Servidor de correo global del sistema
                        <span class="divider">·</span>
                        <i class="bi bi-shield-lock me-1"></i>
                        Solo Owner
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.error-test')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-flask me-1"></i> Probar Errores
                </a>
                <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             style="background: linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46;" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
             style="background: linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b;" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    <?php endif; ?>

    
    <div class="row g-4">

        
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <h5 class="ui-card-title">
                    <i class="bi bi-server"></i> Servidor SMTP
                </h5>
                <p class="ui-card-subtitle">Configuración del servidor de correo electrónico utilizado por el sistema.</p>

                <form method="POST" action="<?php echo e(route('owner.smtp-settings.update')); ?>" class="ui-card-body">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">

                        
                        <div class="col-md-3">
                            <label class="ui-label" for="mail_mailer">Mailer</label>
                            <select name="mail_mailer" id="mail_mailer" class="ui-select">
                                <option value="smtp" <?php echo e($settings['mail_mailer'] == 'smtp' ? 'selected' : ''); ?>>SMTP</option>
                                <option value="log" <?php echo e($settings['mail_mailer'] == 'log' ? 'selected' : ''); ?>>Log (solo pruebas)</option>
                                <option value="mail" <?php echo e($settings['mail_mailer'] == 'mail' ? 'selected' : ''); ?>>PHP Mail</option>
                                <option value="sendmail" <?php echo e($settings['mail_mailer'] == 'sendmail' ? 'selected' : ''); ?>>Sendmail</option>
                            </select>
                        </div>

                        
                        <div class="col-md-4">
                            <label class="ui-label" for="mail_host">Host</label>
                            <input type="text" name="mail_host" id="mail_host" class="ui-input"
                                   value="<?php echo e(old('mail_host', $settings['mail_host'])); ?>"
                                   placeholder="smtp.ejemplo.com">
                        </div>

                        
                        <div class="col-md-2">
                            <label class="ui-label" for="mail_port">Puerto</label>
                            <input type="text" name="mail_port" id="mail_port" class="ui-input"
                                   value="<?php echo e(old('mail_port', $settings['mail_port'])); ?>"
                                   placeholder="465">
                        </div>

                        
                        <div class="col-md-3">
                            <label class="ui-label" for="mail_encryption">Encriptación</label>
                            <select name="mail_encryption" id="mail_encryption" class="ui-select">
                                <option value="ssl" <?php echo e($settings['mail_encryption'] == 'ssl' ? 'selected' : ''); ?>>SSL</option>
                                <option value="tls" <?php echo e($settings['mail_encryption'] == 'tls' ? 'selected' : ''); ?>>TLS</option>
                                <option value="null" <?php echo e($settings['mail_encryption'] == 'null' ? 'selected' : ''); ?>>Sin encriptación</option>
                            </select>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="ui-label" for="mail_username">Usuario</label>
                            <input type="text" name="mail_username" id="mail_username" class="ui-input"
                                   value="<?php echo e(old('mail_username', $settings['mail_username'])); ?>"
                                   placeholder="correo@ejemplo.com">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="ui-label" for="mail_password">Contraseña</label>
                            <input type="password" name="mail_password" id="mail_password" class="ui-input"
                                   placeholder="•••••••• (dejar vacío para mantener)">
                            <span class="form-helper">Dejar en blanco para mantener la contraseña actual.</span>
                        </div>

                        
                        <div class="col-md-6">
                            <label class="ui-label" for="mail_from_address">Dirección Desde (From)</label>
                            <input type="email" name="mail_from_address" id="mail_from_address" class="ui-input"
                                   value="<?php echo e(old('mail_from_address', $settings['mail_from_address'])); ?>"
                                   placeholder="no-reply@ejemplo.com">
                        </div>

                        
                        <div class="col-md-6">
                            <label class="ui-label" for="mail_from_name">Nombre Desde (From Name)</label>
                            <input type="text" name="mail_from_name" id="mail_from_name" class="ui-input"
                                   value="<?php echo e(old('mail_from_name', $settings['mail_from_name'])); ?>"
                                   placeholder="Mi Empresa">
                        </div>

                        
                        <div class="col-12">
                            <div class="smtp-alert-box">
                                <div class="d-flex align-items-start gap-2 mb-2">
                                    <i class="bi bi-exclamation-triangle-fill alert-icon mt-1"></i>
                                    <div>
                                        <strong class="d-block" style="color:#92400e; font-size:.9rem;">Correo de Alertas de Error</strong>
                                        <span style="color:#78350f; font-size:.83rem;">Destinatario de todos los correos de error/alerta del sistema. Recibirá notificaciones de cualquier error que ocurra en CUALQUIERA de las instancias.</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <input type="email" name="error_alert_email" class="ui-input"
                                           value="<?php echo e(old('error_alert_email', $settings['error_alert_email'])); ?>"
                                           placeholder="alertas@miempresa.com">
                                </div>
                            </div>
                        </div>

                    </div>

                    
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3" style="border-top: 1px solid #f1f5f9;">
                        <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                            <i class="bi bi-x-lg me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                            <i class="bi bi-check-lg me-1"></i> Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background: linear-gradient(90deg, #10b981, rgba(255,255,255,.3));"></div>
                <h5 class="ui-card-title">
                    <i class="bi bi-send-check" style="color:#10b981;"></i> Probar Conexión
                </h5>
                <p class="ui-card-subtitle">Envía un correo de prueba para verificar la configuración SMTP.</p>

                <form method="POST" action="<?php echo e(route('owner.smtp-settings.test')); ?>" class="ui-card-body">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="ui-label" for="test_email">Correo de prueba</label>
                        <input type="email" name="test_email" id="test_email" class="ui-input"
                               placeholder="tucorreo@ejemplo.com" required>
                    </div>
                    <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill"
                            style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">
                        <i class="bi bi-send me-1"></i> Enviar Prueba
                    </button>
                </form>

                <hr>

                <div class="smtp-info-panel">
                    <p>
                        <i class="bi bi-info-circle me-1" style="color:#6366f1;"></i>
                        Esta configuración es <strong>global</strong> y aplica a todas las instancias.
                    </p>
                    <p>
                        <i class="bi bi-shield-lock me-1" style="color:#6366f1;"></i>
                        Solo el usuario <strong>Owner</strong> puede modificarla.
                    </p>
                </div>
            </div>
        </div>

    </div>
    

</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/smtp-settings.blade.php ENDPATH**/ ?>