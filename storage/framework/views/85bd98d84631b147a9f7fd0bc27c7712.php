<?php $__env->startSection('title', 'Editar Usuario - ' . $instance->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php
    $hasInstanceRoles = $instanceRoles->isNotEmpty();
?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Editar Usuario</h2>
                    <p class="mb-0 opacity-75"><?php echo e($instance->nombre); ?> &middot; <?php echo e($instance->businessType?->nombre ?? 'Sin tipo'); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('owner.instances.users.update', [$instance, $user])); ?>" id="instanceForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="ui-input rounded-4 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $user->name)); ?>" required placeholder="Nombre completo">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="ui-input rounded-4 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $user->email)); ?>" required placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Nueva Contrase&ntilde;a <small class="text-muted fw-normal">(dejar en blanco para no cambiar)</small></label>
                                <input type="password" name="password" class="ui-input rounded-4 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Nueva contrase&ntilde;a">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Confirmar Contrase&ntilde;a</label>
                                <input type="password" name="password_confirmation" class="ui-input rounded-4" placeholder="Confirmar contrase&ntilde;a">
                            </div>
                        </div>

                        <?php if($hasInstanceRoles): ?>
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Rol de Instancia (m&oacute;dulos visibles)</label>
                            <select name="instance_role_id" class="ui-select rounded-4 <?php $__errorArgs = ['instance_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">— Sin rol de instancia (usa configuraci&oacute;n del tipo de negocio) —</option>
                                <?php $__currentLoopData = $instanceRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ir): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ir->id); ?>" <?php echo e(old('instance_role_id', $user->instance_role_id) == $ir->id ? 'selected' : ''); ?>>
                                    <?php echo e($ir->name); ?> (<?php echo e($ir->visibleModules()->count()); ?> m&oacute;dulos)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Define qu&eacute; m&oacute;dulos ve este usuario en el sidebar.</small>
                            <?php $__errorArgs = ['instance_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small d-none d-md-inline">
            <i class="bi bi-info-circle me-1"></i> Editando usuario: <?php echo e($user->name); ?>

        </span>
        <div class="d-flex gap-2 ms-auto">
            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-outline rounded-4 px-4">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-4 px-5 fw-bold shadow-sm" style="background:#f59e0b;border-color:#f59e0b;color:#000">
                <i class="bi bi-save me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/users/edit.blade.php ENDPATH**/ ?>