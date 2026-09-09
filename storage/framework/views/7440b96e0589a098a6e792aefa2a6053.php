<?php $__env->startSection('title', 'Historial de Pagos'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-1">
            <h4 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Historial de Pagos</h4>
            <a href="<?php echo e(route('suscripcion.index')); ?>" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver a Suscripción
            </a>
        </div>
        <p class="text-muted small mb-0">Todos los pagos registrados de <strong><?php echo e($instance->nombre); ?></strong>.</p>
    </div>

    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Mes pagado</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Referencia</th>
                            <th>Fecha de pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="px-4 py-3 fw-semibold"><?php echo e(optional($pago->mes_pagado)->format('m/Y') ?? '—'); ?></td>
                            <td>RD$ <?php echo e(number_format($pago->monto, 2)); ?></td>
                            <td><span class="badge bg-light text-dark border rounded-pill"><?php echo e(ucfirst($pago->metodo_pago ?? '—')); ?></span></td>
                            <td><code><?php echo e($pago->referencia_externa ?? '—'); ?></code></td>
                            <td><?php echo e(optional($pago->fecha_pago)->format('d/m/Y h:i A') ?? '—'); ?></td>
                            <td>
                                <?php if($pago->estado_pago === 'completado' || $pago->estado_pago === 'pagado'): ?>
                                    <span class="badge bg-success rounded-pill">Confirmado</span>
                                <?php elseif($pago->estado_pago === 'pendiente'): ?>
                                    <span class="badge bg-warning text-dark rounded-pill">Pendiente</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill"><?php echo e(ucfirst($pago->estado_pago)); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-receipt fs-2 d-block mb-2"></i>
                                Aún no hay pagos registrados.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($pagos->hasPages()): ?>
                <div class="card-footer bg-white border-0">
                    <?php echo e($pagos->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/suscripcion/pagos.blade.php ENDPATH**/ ?>