<?php $__env->startSection('title', 'Nuevo Conduce'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            
            <div class="ui-header mb-4" style="--delay:0s">
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="bubble"></div>
                <div class="ui-header-body">
                    <div class="ui-header-left">
                        <div class="ui-avatar-circle">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <h4 class="ui-header-title">Nuevo Conduce</h4>
                            <div class="ui-header-meta">
                                <i class="bi bi-plus-circle me-1"></i>
                                <span>Nota de entrega de productos al cliente</span>
                            </div>
                        </div>
                    </div>
                    <div class="ui-header-actions">
                        <a href="<?php echo e(route('conduces.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                            <i class="bi bi-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
            </div>

            
            <?php if(session('error')): ?>
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            
            <?php if($errors->any()): ?>
                <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="ui-card-title"><i class="bi bi-truck"></i>Detalles del Conduce</h5>
                </div>

                <form method="POST" action="<?php echo e(route('conduces.store')); ?>" id="formConduce">
                    <?php echo csrf_field(); ?>
                    <div class="ui-card-body pt-0">
                        <?php echo $__env->make('conduces._form', ['conduce' => null, 'clientes' => $clientes, 'productos' => $productos], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo Conduce</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('conduces.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="formConduce" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Conduce
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.conduceData = { id: null, items: [], descuentos: [] };
});
</script>
<script src="<?php echo e(asset('js/conduces.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/create.blade.php ENDPATH**/ ?>