<?php $__env->startSection('title', 'Editar ' . $conduce->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-sticky-bar {
    background: #0f172a;
    border-top-color: #fbbf24;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
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
                    <h4 class="ui-header-title">Editar Conduce</h4>
                    <div class="ui-header-meta">
                        <?php echo e($conduce->numero); ?> · Estado:
                        <span class="badge bg-<?php echo e($conduce->estado_color); ?>"><?php echo e($conduce->estado_label); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('conduces.show', $conduce)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="<?php echo e(route('conduces.update', $conduce)); ?>" id="instanceForm">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <?php echo $__env->make('conduces._form', ['conduce' => $conduce, 'clientes' => $clientes, 'productos' => $productos], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </form>

    <div class="ui-sticky-bar">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small d-none d-md-inline">
                <i class="bi bi-info-circle me-1"></i> Editando conduce: <?php echo e($conduce->numero); ?>

            </span>
            <div class="d-flex gap-2 ms-auto">
                <a href="<?php echo e(route('conduces.show', $conduce)); ?>" class="ui-btn ui-btn-ghost rounded-pill px-4">Cancelar</a>
                <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    window.conduceData = {
        id: <?php echo e($conduce->id); ?>,
        items: <?php echo json_encode($conduce->items, 15, 512) ?>,
        descuentos: <?php echo json_encode($conduce->descuentos ?? [], 15, 512) ?>
    };
});
</script>
<script src="<?php echo e(asset('js/conduces.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/conduces/edit.blade.php ENDPATH**/ ?>