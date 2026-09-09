<?php $__env->startSection('title', 'Editar Secuencia NCF'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Secuencia NCF</h4>
                    <div class="ui-header-meta"><?php echo e($ncf->prefijo); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('ncf.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent amber"></div>
        <form id="ncfForm" action="<?php echo e(route('ncf.update', $ncf)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="ui-card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información del NCF
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label fw-bold">Nombre del Comprobante</label>
                        <input type="text" name="nombre" class="ui-input" value="<?php echo e($ncf->nombre); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Prefijo</label>
                        <input type="text" name="prefijo" class="ui-input" maxlength="3" value="<?php echo e($ncf->prefijo); ?>" required onkeyup="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Desde</label>
                        <input type="number" name="desde" class="ui-input" value="<?php echo e($ncf->desde); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Hasta</label>
                        <input type="number" name="hasta" class="ui-input" value="<?php echo e($ncf->hasta); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Número Actual</label>
                        <input type="number" name="actual" class="ui-input" value="<?php echo e($ncf->actual); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input" value="<?php echo e($ncf->fecha_vencimiento); ?>" required>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <span class="fw-semibold" style="color: var(--accent);"><i class="bi bi-shield-check me-1"></i> Editando: <?php echo e($ncf->prefijo); ?></span>
        <button type="submit" form="ncfForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm">
            <i class="bi bi-check-circle me-1"></i> Actualizar Secuencia
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ncf/edit.blade.php ENDPATH**/ ?>