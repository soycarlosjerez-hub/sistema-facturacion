<?php $__env->startSection('title', 'Procesadores de Pago'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Procesadores de Pago</h4>
                    <div class="ui-header-meta">Gestiona los procesadores y sus credenciales API</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('payment-processors.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Procesador
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card overflow-hidden" style="--delay:.1s">
        <div class="ui-card-accent blue"></div>
        <div class="table-responsive">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Tipo</th>
                        <th>Comisión</th>
                        <th>Entorno</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $procesadores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold"><?php echo e($p->nombre); ?></span>
                                <?php if($p->api_key): ?>
                                    <i class="bi bi-key text-muted ms-1 small" title="API configurada"></i>
                                <?php endif; ?>
                            </td>
                            <td><span class="ui-badge ui-badge-neutral"><?php echo e(ucfirst($p->tipo)); ?></span></td>
                            <td>
                                <small class="text-muted"><?php echo e(number_format($p->comision_porcentaje, 2)); ?>% + RD$ <?php echo e(number_format($p->comision_fija, 2)); ?></small>
                            </td>
                            <td>
                                <?php if($p->api_environment === 'production'): ?>
                                    <span class="ui-badge ui-badge-danger">Producción</span>
                                <?php elseif($p->api_environment === 'sandbox'): ?>
                                    <span class="ui-badge ui-badge-success">Sandbox</span>
                                <?php else: ?>
                                    <span class="ui-badge ui-badge-neutral">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($p->activo): ?>
                                    <span class="ui-badge ui-badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="ui-badge ui-badge-neutral">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?php echo e(route('payment-processors.edit', $p)); ?>" class="ui-action ui-action-edit me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('payment-processors.destroy', $p)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="ui-action ui-action-delete" title="Eliminar" onclick="event.preventDefault();UI.confirm.delete('<?php echo e(route('payment-processors.destroy', $p)); ?>', '<?php echo e(addslashes($p->nombre)); ?>')"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="ui-empty-state">
                                    <i class="bi bi-credit-card"></i>
                                    <p>No hay procesadores registrados</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($procesadores->hasPages()): ?>
        <div class="border-0 py-3 px-4" style="background:transparent;">
            <?php echo e($procesadores->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/payment-processors/index.blade.php ENDPATH**/ ?>