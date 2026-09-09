<?php $__env->startSection('title', isset($modulo) ? "Editar Módulo - {$modulo->label}" : 'Nuevo Módulo'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

<div class="ui-header mb-4" style="--delay:0s">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-grid"></i>
            </div>
            <div>
                <h4 class="ui-header-title">
                    <?php echo e(isset($modulo) ? 'Editar Módulo' : 'Nuevo Módulo'); ?>

                </h4>
                <?php if(isset($modulo)): ?>
                    <div class="ui-header-meta">
                        <i class="bi bi-code-slash me-1"></i><?php echo e($modulo->key); ?> · <?php echo e($modulo->label); ?>

                    </div>
                <?php else: ?>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>Crea un nuevo módulo para asignar a tipos de negocio y roles.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="ui-header-actions">
            <a href="<?php echo e(route('owner.modules.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body p-4">
                <form method="POST" action="<?php echo e(isset($modulo) ? route('owner.modules.update', $modulo) : route('owner.modules.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if(isset($modulo)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Key <span class="text-danger">*</span></label>
                        <input type="text" name="key" class="form-control form-control-modern <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('key', $modulo->key ?? '')); ?>" required
                               placeholder="ej: mi-modulo" <?php echo e(isset($modulo) ? 'readonly' : ''); ?>>
                        <small class="text-muted">Identificador único. Usa guiones, sin espacios. No se puede cambiar después de crear.</small>
                        <?php $__errorArgs = ['key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="label" class="form-control form-control-modern <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('label', $modulo->label ?? '')); ?>" required placeholder="Ej: Mi Módulo">
                        <?php $__errorArgs = ['label'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Icono (Bootstrap Icons)</label>
                        <input type="text" name="icon" class="form-control form-control-modern <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('icon', $modulo->icon ?? 'bi-circle')); ?>" placeholder="Ej: bi-box-seam">
                        <small class="text-muted">Clase del icono Bootstrap. <a href="https://icons.getbootstrap.com/" target="_blank">Ver todos</a></small>
                        <?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" class="form-select form-select-modern <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Seleccionar...</option>
                                <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat); ?>" <?php echo e(old('categoria', $modulo->categoria ?? '') === $cat ? 'selected' : ''); ?>>
                                    <?php echo e(ucfirst($cat)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <option value="otro" <?php echo e(old('categoria', $modulo->categoria ?? '') === 'otro' ? 'selected' : ''); ?>>Otro</option>
                            </select>
                            <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Orden</label>
                            <input type="number" name="orden" class="form-control form-control-modern <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('orden', $modulo->orden ?? 0)); ?>" min="0">
                            <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="checkbox" name="activo" class="form-check-input-modern" id="activo"
                                       value="1" <?php echo e(old('activo', $modulo->activo ?? true) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-bold small" for="activo">Activo</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo e(route('owner.modules.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
                        <button type="submit" class="ui-btn ui-btn-primary rounded-pill px-4 fw-bold">
                            <i class="bi bi-check-lg me-2"></i><?php echo e(isset($modulo) ? 'Guardar Cambios' : 'Crear Módulo'); ?>

                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/modules/form.blade.php ENDPATH**/ ?>