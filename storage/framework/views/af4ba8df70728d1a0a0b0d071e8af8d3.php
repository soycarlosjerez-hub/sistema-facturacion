<?php $__env->startSection('title', 'Verificar correo'); ?>
<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-sm-5 text-center">
                    <i class="bi bi-envelope-check display-3 text-success"></i>
                    <h4 class="fw-bold mt-3">Verifica tu correo electrónico</h4>
                    <p class="text-muted mb-4">
                        Gracias por registrarte. Antes de continuar, verifica tu correo electrónico haciendo clic en el enlace que te enviamos.
                    </p>

                    <?php if(session('status') == 'verification-link-sent'): ?>
                        <div class="alert alert-success border-0 rounded-3 shadow-sm mb-4">
                            <i class="bi bi-check-circle me-1"></i> Se ha enviado un nuevo enlace de verificación a tu correo.
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-center gap-3">
                        <form method="POST" action="<?php echo e(route('verification.send')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-send me-1"></i> Reenviar verificación
                            </button>
                        </form>

                        <form method="POST" action="<?php echo e(route('logout')); ?>">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/auth/verify-email.blade.php ENDPATH**/ ?>