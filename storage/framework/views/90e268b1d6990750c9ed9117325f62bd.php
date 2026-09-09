<?php $__env->startSection('title', 'Historial de Pagos - ' . $instance->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Historial de Pagos</h2>
                    <p class="mb-0 opacity-75"><?php echo e($instance->nombre); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.instances.pagos.create', $instance)); ?>" class="ui-btn ui-btn-solid" style="background:#10b981;border-color:#10b981">
                    <i class="bi bi-plus-lg me-2"></i>Registrar Pago
                </a>
                <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#10b981"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Mes Pagado</th>
                        <th>Monto</th>
                        <th>M&eacute;todo</th>
                        <th>Fecha de Pago</th>
                        <th>Estado</th>
                        <th>Registrado por</th>
                        <th class="pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4"><?php echo e($pago->id); ?></td>
                        <td class="fw-bold"><?php echo e($pago->mes_pagado->isoFormat('MMMM YYYY')); ?></td>
                        <td><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($pago->monto, 2)); ?></td>
                        <td><?php echo e($pago->metodo_pago ?? '—'); ?></td>
                        <td><?php echo e($pago->fecha_pago->format('d/m/Y h:i A')); ?></td>
                        <td>
                            <?php if(in_array($pago->estado_pago, ['completado', 'pagado'])): ?>
                                <span class="badge bg-success rounded-pill">Confirmado</span>
                            <?php elseif($pago->estado_pago === 'pendiente'): ?>
                                <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                            <?php else: ?>
                                <span class="badge bg-secondary rounded-pill"><?php echo e(ucfirst($pago->estado_pago)); ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($pago->registradoPor?->name ?? '—'); ?></td>
                        <td class="pe-4">
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('owner.instances.pagos.edit', [$instance, $pago])); ?>" class="btn btn-sm btn-outline-primary rounded-pill" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($pago->estado_pago === 'pendiente'): ?>
                                    <form method="POST" action="<?php echo e(route('owner.instances.pagos.confirmar', [$instance, $pago])); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button class="btn btn-sm btn-success rounded-pill" onclick="return confirm('¿Confirmar este pago? La instancia quedará al día.')">
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay pagos registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($pagos->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 py-3 px-4">
            <?php echo e($pagos->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/pagos/index.blade.php ENDPATH**/ ?>