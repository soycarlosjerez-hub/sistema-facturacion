<?php $__env->startSection('title', 'Editar Producto'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#6366f1;--accent-rgb:99,102,241;--accent-hover:#4f46e5;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="ui-header-title">Editar Producto</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        Actualiza la información del producto en el inventario
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('productos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(99,102,241,.05);border-left:4px solid #4f46e5 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#4f46e5;background:rgba(99,102,241,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Estás editando el producto:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;"><?php echo e($producto->nombre); ?></strong>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s;">
        <div class="ui-card-accent"></div>
        <form id="productForm" action="<?php echo e(route('productos.update', $producto)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('productos.form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#4f46e5;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: <?php echo e($producto->nombre); ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('productos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="productForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-cloud-arrow-up me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/productos/edit.blade.php ENDPATH**/ ?>