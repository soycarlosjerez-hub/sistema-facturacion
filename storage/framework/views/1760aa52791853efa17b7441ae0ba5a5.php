<div class="p-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Historial — <?php echo e($mesa->nombre ?? 'Mesa '.$mesa->numero); ?></h6>
    <?php $__empty_0 = true; $__currentLoopData = $ordenes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orden): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
    <div class="ui-card rounded-3 mb-2" style="--delay:0s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="ui-badge ui-badge-primary">#<?php echo e($orden->id); ?></span>
                    <small class="text-muted ms-2"><?php echo e($orden->created_at->format('d/m/Y h:i A')); ?></small>
                </div>
                <span class="fw-bold" style="color:#10b981;">RD$ <?php echo e(number_format($orden->total, 2)); ?></span>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <small class="text-muted">
                    <i class="bi bi-person me-1"></i><?php echo e($orden->cliente?->nombre ?? $orden->cliente_nombre ?? 'Consumidor Final'); ?>

                </small>
                <small class="text-muted">·</small>
                <small class="text-muted">
                    <i class="bi bi-credit-card me-1"></i><?php echo e($orden->pagos->first()?->metodo_pago ?? $orden->metodo_pago ?? '—'); ?>

                </small>
                <small class="text-muted">·</small>
                <small class="text-muted">
                    <span class="ui-badge <?php echo e($orden->estado === 'facturada' ? 'ui-badge-success' : 'ui-badge-neutral'); ?>"><?php echo e($orden->estado); ?></span>
                </small>
            </div>
            <?php if($orden->detalles->count() > 0): ?>
            <div class="mt-2">
                <small class="text-muted d-block">
                    <?php echo e($orden->detalles->take(3)->pluck('producto.nombre')->implode(', ')); ?>

                    <?php if($orden->detalles->count() > 3): ?>
                        <span class="text-muted"> y <?php echo e($orden->detalles->count() - 3); ?> más</span>
                    <?php endif; ?>
                </small>
            </div>
            <?php endif; ?>
            <div class="mt-2 d-flex gap-1">
                <a href="<?php echo e(route('restaurante.mesa.ticket', ['mesa' => $mesa, 'venta_id' => $orden->id])); ?>" target="_blank" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-receipt"></i> Ticket
                </a>
                <button type="button" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill" onclick="reimprimirTicket(<?php echo e($mesa->id); ?>, <?php echo e($orden->id); ?>)">
                    <i class="bi bi-printer"></i> Reimprimir
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
    <div class="ui-empty-state">
        <i class="bi bi-inbox"></i>
        <p>Esta mesa no tiene órdenes cerradas</p>
    </div>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/sistema-facturacion/resources/views/restaurante/_historial.blade.php ENDPATH**/ ?>