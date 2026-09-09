<?php $__env->startSection('title', 'Nuevo Inquilino'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-floating-modern { position: relative; margin-bottom: 1rem; }
    .form-floating-modern .form-icon { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #94a3b8; z-index: 5; font-size: 1.1rem; pointer-events: none; }
    .form-floating-modern .form-control { padding-left: 42px; height: 50px; border-radius: 12px; border: 1.5px solid #e2e8f0; }
    .form-floating-modern .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16,185,129,.12); }
    .form-floating-modern .form-label-float { position: absolute; top: 50%; left: 42px; transform: translateY(-50%); color: #94a3b8; transition: all .2s; pointer-events: none; background: transparent; padding: 0 4px; }
    .form-floating-modern .form-control:focus + .form-label-float,
    .form-floating-modern .form-control:not(:placeholder-shown) + .form-label-float { top: -10px; left: 36px; font-size: .75rem; color: #10b981; background: #fff; }
    textarea.form-control { height: auto !important; padding-top: 14px !important; }
    textarea.form-control + .form-label-float { top: 14px !important; }
    textarea.form-control:focus + .form-label-float,
    textarea.form-control:not(:placeholder-shown) + .form-label-float { top: -10px !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;--delay:0s;">
    <div class="ui-header mb-4" style="background: linear-gradient(135deg, #10b981, #06b6d4);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-person-plus"></i></div>
                <div>
                    <h4 class="ui-header-title">Nuevo Inquilino</h4>
                    <div class="ui-header-meta">Registra un nuevo inquilino</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('alquileres.inquilinos.index')); ?>" class="ui-btn ui-btn-primary rounded-pill">
                    <i class="bi bi-eye me-1"></i> Ver Todos
                </a>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('alquileres.inquilinos.store')); ?>" method="POST" id="instanceForm">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s;">
                    <div class="ui-card-accent green"></div>
                    <div class="ui-card-body p-4">
                        <div class="ui-card-title"><i class="bi bi-person-badge"></i>Datos del Inquilino</div>
                        <div class="ui-card-subtitle">Complete los campos para registrar el nuevo inquilino</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre" class="ui-label">Nombre completo <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre')); ?>" placeholder="" required>
                                    </div>
                                    <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cedula" class="ui-label">Cédula</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <input type="text" name="cedula" id="cedula" class="ui-input" value="<?php echo e(old('cedula')); ?>" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="telefono" class="ui-label">Teléfono</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="telefono" id="telefono" class="ui-input" value="<?php echo e(old('telefono')); ?>" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="ui-label">Email</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="email" class="ui-input" value="<?php echo e(old('email')); ?>" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="direccion" class="ui-label">Dirección</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="direccion" id="direccion" class="ui-input" value="<?php echo e(old('direccion')); ?>" placeholder="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="notas" class="ui-label">Notas</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-sticky"></i></span>
                                        <textarea name="notas" id="notas" class="ui-textarea" placeholder="" rows="3"><?php echo e(old('notas')); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ui-card" style="--delay:.2s;">
                    <div class="ui-card-accent green"></div>
                    <div class="ui-card-body p-4 text-center">
                        <i class="bi bi-person-circle" style="font-size:4rem;color:#10b981;"></i>
                        <p class="text-muted small mt-2 mb-0">Los inquilinos pueden tener uno o más contratos de alquiler activos.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div style="height: 80px;"></div>
</div>
<?php $__env->stopSection(); ?>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color:#10b981;"></i>
                <span class="fw-semibold d-none d-sm-inline">Creando nuevo inquilino</span>
            </div>
            <div>
                <a href="<?php echo e(route('alquileres.inquilinos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill me-2">Cancelar</a>
                <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg me-2"></i>Guardar Inquilino
                </button>
            </div>
        </div>
    </div>
</div>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/alquileres/inquilinos/create.blade.php ENDPATH**/ ?>