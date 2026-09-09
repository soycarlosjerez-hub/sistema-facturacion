<?php $__env->startSection('title', 'Nueva Licencia de Software'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <div class="ui-header-title">Nueva Licencia de Software</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Registra una nueva clave de licencia
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('licencias-software.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="licenciaForm" action="<?php echo e(route('licencias-software.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #0891b2;">
                            <i class="bi bi-box-seam me-2"></i>Producto
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="producto_id" class="ui-label">Producto</label>
                                <select name="producto_id" id="producto_id" class="ui-select <?php $__errorArgs = ['producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Seleccionar producto --</option>
                                    <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($producto->id); ?>" <?php echo e(old('producto_id') == $producto->id ? 'selected' : ''); ?>>
                                        <?php echo e($producto->nombre); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                            <i class="bi bi-shield-lock me-2"></i>Clave de Licencia
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="clave_licencia" class="ui-label">Clave de Licencia <span class="text-danger">*</span></label>
                                <input type="text" name="clave_licencia" id="clave_licencia" class="ui-input <?php $__errorArgs = ['clave_licencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('clave_licencia')); ?>" required>
                                <small class="text-muted">Ingresa la clave única de activación del software</small>
                                <?php $__errorArgs = ['clave_licencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #059669;">
                            <i class="bi bi-gear me-2"></i>Configuración
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipo_licencia" class="ui-label">Tipo de Licencia</label>
                                <select name="tipo_licencia" id="tipo_licencia" class="ui-select <?php $__errorArgs = ['tipo_licencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="perpetua" <?php echo e(old('tipo_licencia') == 'perpetua' ? 'selected' : ''); ?>>Perpetua</option>
                                    <option value="suscripcion" <?php echo e(old('tipo_licencia') == 'suscripcion' ? 'selected' : ''); ?>>Suscripción</option>
                                    <option value="open_source" <?php echo e(old('tipo_licencia') == 'open_source' ? 'selected' : ''); ?>>Open Source</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="plataforma" class="ui-label">Plataforma</label>
                                <select name="plataforma" id="plataforma" class="ui-select <?php $__errorArgs = ['plataforma'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Windows" <?php echo e(old('plataforma') == 'Windows' ? 'selected' : ''); ?>>Windows</option>
                                    <option value="macOS" <?php echo e(old('plataforma') == 'macOS' ? 'selected' : ''); ?>>macOS</option>
                                    <option value="Linux" <?php echo e(old('plataforma') == 'Linux' ? 'selected' : ''); ?>>Linux</option>
                                    <option value="Cloud" <?php echo e(old('plataforma') == 'Cloud' ? 'selected' : ''); ?>>Cloud/Web</option>
                                    <option value="Multi-plataforma" <?php echo e(old('plataforma') == 'Multi-plataforma' ? 'selected' : ''); ?>>Multi-plataforma</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="usuario_asignado" class="ui-label">Usuario Asignado</label>
                                <input type="text" name="usuario_asignado" id="usuario_asignado" class="ui-input <?php $__errorArgs = ['usuario_asignado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('usuario_asignado')); ?>" placeholder="Nombre del usuario o empresa">
                            </div>
                            <div class="col-12">
                                <label for="fecha_vencimiento" class="ui-label">Fecha de Vencimiento</label>
                                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="ui-input <?php $__errorArgs = ['fecha_vencimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_vencimiento')); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                            <i class="bi bi-journal-text me-2"></i>Notas y Estado
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="notas" class="ui-label">Notas</label>
                                <textarea name="notas" id="notas" class="ui-textarea <?php $__errorArgs = ['notas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Notas adicionales sobre la licencia"><?php echo e(old('notas')); ?></textarea>
                                <?php $__errorArgs = ['notas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="licencia_activa" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(6,182,212,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="licencia_activa" id="licencia_activa" <?php echo e(old('licencia_activa', true) ? 'checked' : ''); ?> role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="licencia_activa">Licencia Activa</label>
                                    </div>
                                    <small class="text-muted">Si está inactiva no podrá usarse.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#06b6d4;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva licencia</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('licencias-software.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="licenciaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Licencia
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/licencias-software/create.blade.php ENDPATH**/ ?>