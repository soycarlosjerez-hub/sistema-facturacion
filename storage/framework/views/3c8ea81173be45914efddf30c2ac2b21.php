<div class="ui-card-title">
    <i class="bi bi-person-plus"></i>Usuarios Administradores
</div>
<div class="ui-card-subtitle">
    Crea un usuario administrador adicional para la instancia, o salta este paso y agrégalo después desde el panel de usuarios.
</div>

<form action="<?php echo e(route('setup.step')); ?>" method="POST" class="row g-4">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="step" value="usuario-admin">

    <div class="col-md-6">
        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
        <input type="text" name="name" id="admin_name" class="ui-input" value="<?php echo e(old('name')); ?>" placeholder="Nombre completo" required>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="text-danger"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Correo electrónico <span class="text-danger">*</span></label>
        <input type="email" name="email" id="admin_email" class="ui-input" value="<?php echo e(old('email')); ?>" placeholder="correo@empresa.com" required>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="text-danger"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Contraseña <span class="text-danger">*</span></label>
        <input type="password" name="password" id="admin_password" class="ui-input" placeholder="Mín. 12 caracteres" required>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="text-danger"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="ui-label">Confirmar contraseña <span class="text-danger">*</span></label>
        <input type="password" name="password_confirmation" id="admin_password_confirmation" class="ui-input" placeholder="Repite la contraseña" required>
    </div>

    <div class="col-12 mt-4">
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <button type="submit" name="skip" value="1" class="ui-btn ui-btn-ghost ui-btn-pill">
                    <i class="bi bi-forward-skip me-2"></i>Saltar este paso
                </button>
                <button type="submit" class="ui-btn ui-btn-solid ui-btn-pill px-5">
                    <i class="bi bi-person-check me-2"></i>Crear Usuario Admin
                </button>
            </div>
        </div>
    </div>
</form><?php /**PATH /var/www/html/sistema-facturacion/resources/views/setup/_step-usuario-admin.blade.php ENDPATH**/ ?>