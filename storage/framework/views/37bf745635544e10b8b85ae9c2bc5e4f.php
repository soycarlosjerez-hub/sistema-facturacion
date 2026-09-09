<?php if($instanciasConAtraso->isNotEmpty()): ?>
<div class="ui-card mb-4" style="--delay:.3s">
    <div class="ui-card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-clock-history me-2" style="color:#f59e0b"></i>
                Instancias con Atraso en Pagos
            </h5>
            <span class="ui-badge ui-badge-warning rounded-pill"><?php echo e($instanciasConAtraso->count()); ?></span>
        </div>
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th>Instancia</th>
                        <th>Tipo</th>
                        <th>Propietario</th>
                        <th>Costo</th>
                        <th>Deuda</th>
                        <th>Último Pago</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $instanciasConAtraso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="fw-bold">
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="text-decoration-none"><?php echo e($instance->nombre); ?></a>
                        </td>
                        <td><span class="ui-badge ui-badge-<?php echo e($instance->businessType?->color ?? 'secondary'); ?> rounded-pill"><?php echo e($instance->businessType?->nombre ?? '—'); ?></span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($instance->owner_nombre): ?>
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:rgba(139,92,246,.12);color:#8b5cf6;font-size:11px;font-weight:600;flex-shrink:0;">
                                        <?php echo e(strtoupper(substr($instance->owner_nombre, 0, 1))); ?>

                                    </div>
                                    <div>
                                        <div class="small fw-semibold"><?php echo e($instance->owner_nombre); ?></div>
                                        <div class="text-muted" style="font-size:10px;"><?php echo e($instance->owner_email); ?></div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($instance->costo_mensual ?? 0, 2)); ?></td>
                        <td class="fw-bold text-danger"><?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($instance->deudaEstimada(), 2)); ?></td>
                        <td>
                            <?php $ultimo = $instance->ultimoPago->first(); ?>
                            <?php if($ultimo): ?>
                                <?php echo e($ultimo->mes_pagado->isoFormat('MMM YYYY')); ?>

                            <?php else: ?>
                                <span class="text-muted">Sin pagos</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="<?php echo e(route('owner.instances.pagos.create', $instance)); ?>" class="ui-btn ui-btn-ghost btn-sm rounded-pill" title="Registrar Pago" style="font-size:.75rem;">
                                <i class="bi bi-cash-coin"></i>
                            </a>
                            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-ghost btn-sm rounded-pill" title="Detalles" style="font-size:.75rem;">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/partials/_overdue_table.blade.php ENDPATH**/ ?>