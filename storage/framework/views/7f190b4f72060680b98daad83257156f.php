<form method="post" action="<?php echo e(route('profile.update')); ?>" class="mt-5 space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('patch'); ?>

    <div>
        <label class="ui-label" for="name"><?php echo e(__('Name')); ?></label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-person"></i></span>
            <input id="name" name="name" type="text" class="ui-input" value="<?php echo e(old('name', $user->name)); ?>" required autofocus autocomplete="name" placeholder="Tu nombre" />
        </div>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
        <label class="ui-label" for="email"><?php echo e(__('Email')); ?></label>
        <div class="ui-input-group mt-1">
            <span class="ui-input-group-text"><i class="bi bi-envelope"></i></span>
            <input id="email" name="email" type="email" class="ui-input" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="username" placeholder="correo@ejemplo.com" />
        </div>
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()): ?>
            <div>
                <p class="fs-6 mt-2 text-secondary">
                    <?php echo e(__('Your email address is unverified.')); ?>


                    <button form="send-verification" class="underline fs-6 text-secondary hover:text-dark rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <?php echo e(__('Click here to re-send the verification email.')); ?>

                    </button>
                </p>

                <?php if(session('status') === 'verification-link-sent'): ?>
                    <p class="mt-2 font-medium fs-6 text-success">
                        <i class="bi bi-check-circle me-1"></i><?php echo e(__('A new verification link has been sent to your email address.')); ?>

                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <form id="send-verification" method="post" action="<?php echo e(route('verification.send')); ?>">
        <?php echo csrf_field(); ?>
    </form>

    <div class="d-flex align-items-center gap-4 mt-4">
        <button type="submit" class="ui-btn ui-btn-primary"><i class="bi bi-check-lg me-1"></i><?php echo e(__('Save')); ?></button>

        <?php if(session('status') === 'profile-updated'): ?>
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
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/profile/partials/update-profile-information-form.blade.php ENDPATH**/ ?>