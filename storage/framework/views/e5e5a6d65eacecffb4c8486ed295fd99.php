<table class="ui-table mb-0">
    <thead>
        <tr>
            <th class="ps-4">Fecha y Hora</th>
            <th>Producto</th>
            <th>Almacén</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Nota / Concepto</th>
            <th class="text-end pe-4">Registrado por</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_0 = true; $__currentLoopData = $movimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <tr>
            <td class="ps-4">
                <div class="small fw-bold text-dark"><?php echo e($m->created_at->format('d/m/Y')); ?></div>
                <div class="text-muted small" style="font-size: 0.7rem;"><?php echo e($m->created_at->format('h:i A')); ?></div>
            </td>
            <td>
                <div class="fw-bold text-dark small"><?php echo e($m->producto?->nombre ?? '—'); ?></div>
                <small class="text-muted" style="font-size: 0.7rem;">ID: <?php echo e($m->producto?->id ?? '—'); ?></small>
            </td>
            <td>
                <span class="badge bg-light text-dark border-0 p-0 fw-bold"><?php echo e($m->almacen?->nombre ?? '—'); ?></span>
            </td>
            <td>
                <?php if($m->tipo === 'entrada'): ?>
                    <span class="ui-badge ui-badge-success">
                        <i class="bi bi-arrow-down-left me-1"></i> Entrada
                    </span>
                <?php else: ?>
                    <span class="ui-badge ui-badge-danger">
                        <i class="bi bi-arrow-up-right me-1"></i> Salida
                    </span>
                <?php endif; ?>
            </td>
            <td>
                <div class="fw-bold <?php echo e($m->tipo === 'entrada' ? 'text-success' : 'text-danger'); ?>">
                    <?php echo e($m->tipo === 'entrada' ? '+' : '-'); ?> <?php echo e($m->cantidad); ?>

                </div>
            </td>
            <td>
                <small class="text-muted"><?php echo e($m->nota ?? 'Sin observaciones'); ?></small>
            </td>
            <td class="text-end pe-4">
                <div class="small fw-bold text-dark"><?php echo e($m->user->name ?? 'Sistema'); ?></div>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
        <tr>
            <td colspan="7" class="text-center py-5">
                <div class="ui-empty-state">
                    <i class="bi bi-arrow-left-right"></i>
                    <p>No hay movimientos registrados.</p>
                </div>
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php /**PATH /var/www/html/sistema-facturacion/resources/views/almacenes/_movimientos-table.blade.php ENDPATH**/ ?>