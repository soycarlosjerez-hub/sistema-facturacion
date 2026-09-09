<?php $__env->startSection('title', 'Instancia Bloqueada'); ?>
<?php $__env->startSection('content'); ?>
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="text-center" style="max-width: 480px;">
        <div class="mb-4">
            <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <i class="bi bi-lock-fill text-danger fs-1"></i>
            </div>
        </div>
        <h2 class="fw-bold mb-2">Instancia Bloqueada</h2>
        <p class="text-muted mb-4">
            <?php if(session('error')): ?>
                <?php echo e(session('error')); ?>

            <?php else: ?>
                Esta instancia ha sido bloqueada. Comuníquese con el administrador del sistema para más información.
            <?php endif; ?>
        </p>
        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('suscripcion.index')); ?>" class="btn btn-success rounded-pill px-5 fw-bold">
                    <i class="bi bi-credit-card me-2"></i>Ver Suscripción y Pagar
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('login')); ?>" class="btn btn-primary rounded-pill px-5 fw-bold">
                <i class="bi bi-box-arrow-right me-2"></i>Ir al Login
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/errors/instancia-bloqueada.blade.php ENDPATH**/ ?>