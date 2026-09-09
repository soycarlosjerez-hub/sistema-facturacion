<form method="post" action="<?php echo e(route('password.update')); ?>" class="mt-5 space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('put'); ?>

    <div>
        <label class="ui-label" for="update_password_current_password"><?php echo e(__('Current Password')); ?></label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-lock"></i></span>
            <input id="update_password_current_password" name="current_password" type="password" class="ui-input" autocomplete="current-password" placeholder="Contraseña actual" />
        </div>
        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="ui-label" for="update_password_password"><?php echo e(__('New Password')); ?></label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-key"></i></span>
            <input id="update_password_password" name="password" type="password" class="ui-input" autocomplete="new-password" placeholder="Nueva contraseña" />
        </div>
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="ui-label" for="update_password_password_confirmation"><?php echo e(__('Confirm Password')); ?></label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-key-fill"></i></span>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="ui-input" autocomplete="new-password" placeholder="Confirma la contraseña" />
        </div>
        <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="d-flex align-items-center gap-4 mt-4">
        <button type="submit" class="ui-btn ui-btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e(__('Save')); ?></button>

        <?php if(session('status') === 'password-updated'): ?>
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="saved-indicator"
            >
                <i class="bi bi-check-circle-fill"></i><?php echo e(__('Saved.')); ?>

            </p>
        <?php endif; ?>
    </div>
</form>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/profile/partials/update-password-form.blade.php ENDPATH**/ ?>