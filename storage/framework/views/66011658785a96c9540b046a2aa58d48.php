<div class="form-floating-modern">
    <i class="bi bi-person form-icon"></i>
    <input type="text" name="name" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('name', $usuario->name ?? '')); ?>" placeholder=" " required maxlength="255">
    <label class="form-label-float" for="name">Nombre completo</label>
    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="form-floating-modern">
    <i class="bi bi-envelope form-icon"></i>
    <input type="email" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('email', $usuario->email ?? '')); ?>" placeholder=" " required maxlength="255">
    <label class="form-label-float" for="email">Correo electrónico</label>
    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-lock form-icon"></i>
            <input type="password" name="password" id="password"
                   class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   placeholder=" " <?php echo e(isset($usuario) ? '' : 'required'); ?> minlength="6">
            <label class="form-label-float" for="password">
                <?php echo e(isset($usuario) ? 'Nueva contraseña (opcional)' : 'Contraseña'); ?>

            </label>
            <div class="password-strength"><div class="password-strength-bar" id="strengthBar"></div></div>
            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-floating-modern">
            <i class="bi bi-shield-check form-icon"></i>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="form-control" placeholder=" " <?php echo e(isset($usuario) ? '' : 'required'); ?> minlength="6">
            <label class="form-label-float" for="password_confirmation">Confirmar contraseña</label>
        </div>
    </div>
</div>
<?php if(isset($usuario)): ?>
    <div class="text-muted small mb-3 ms-1"><i class="bi bi-info-circle me-1"></i>Deja los campos de contraseña vacíos para mantener la actual.</div>
<?php endif; ?>

<?php if(isset($sucursales) && $sucursales->count()): ?>
<div class="form-floating-modern">
    <i class="bi bi-building form-icon"></i>
    <select name="sucursal_id" id="sucursal_id" class="form-select <?php $__errorArgs = ['sucursal_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <option value="">Sin sucursal asignada</option>
        <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($s->id); ?>" <?php echo e(old('sucursal_id', $usuario->sucursal_id ?? '') == $s->id ? 'selected' : ''); ?>><?php echo e($s->nombre); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <label class="form-label-float" for="sucursal_id">Sucursal</label>
    <?php $__errorArgs = ['sucursal_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block ms-5"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/usuarios/_form_fields.blade.php ENDPATH**/ ?>