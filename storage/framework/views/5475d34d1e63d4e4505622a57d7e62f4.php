<?php $__env->startSection('title', 'Compra ' . $compra->folio); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.detalles-table {
    --bs-table-bg: transparent;
    --bs-table-hover-bg: rgba(59,130,246,.04);
    margin: 0;
}
.detalles-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.detalles-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
body.dark-mode .detalles-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .detalles-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
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
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Compra <?php echo e($compra->folio); ?></h4>
                    <div class="ui-header-meta">Detalle completo de la compra y productos recibidos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('compras.edit', $compra)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-pencil-square me-1"></i>Editar
                    </a>
                    <?php if($compra->puede_generar_ecf): ?>
                    <form action="<?php echo e(route('compras.generar-ecf', $compra)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:#06b6d4;border-color:#06b6d4;" onclick="UI.confirm.action({title:'Generar e-CF E41', text:'¿Generar Nota de Compra (E41) para esta compra?', icon:'info', color:'#06b6d4', confirmText:'Generar E41', onSubmit:function(){ this.closest('form').submit(); }})">
                            <i class="bi bi-shield-check me-1"></i>Generar e-CF E41
                        </button>
                    </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('compras.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="ui-stat-label mb-1"><i class="bi bi-truck me-1"></i>Proveedor</div>
                    <div class="ui-stat-value" style="font-size:1rem;"><?php echo e($compra->proveedor->nombre ?? 'N/A'); ?></div>
                    <small class="text-muted">RNC: <?php echo e($compra->proveedor->rnc_cedula ?? '—'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="ui-stat-label mb-1"><i class="bi bi-calendar3 me-1"></i>Fecha</div>
                    <div class="ui-stat-value" style="font-size:1rem;"><?php echo e($compra->fecha ? $compra->fecha->format('d/m/Y') : $compra->created_at->format('d/m/Y')); ?></div>
                    <small class="text-muted"><?php echo e($compra->created_at->format('h:i A')); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3">
                    <div class="ui-stat-label mb-1"><i class="bi bi-tag me-1"></i>Tipo</div>
                    <div class="ui-stat-value" style="font-size:1rem;"><?php echo e($compra->tipoCompra->nombre ?? 'N/A'); ?></div>
                    <small class="text-muted">Registrado por <?php echo e($compra->user->name ?? '—'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-3 text-center">
                    <div class="ui-stat-label mb-1"><i class="bi bi-cash-stack me-1"></i>Total</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($compra->total, 2)); ?></div>
                    <small class="text-muted">Incluye ITBIS</small>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.25s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-list-check me-2"></i>
            Productos recibidos
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table detalles-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">ITBIS %</th>
                            <th class="text-end">Base</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $compra->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold"><?php echo e($detalle->producto->nombre ?? '—'); ?></div>
                                <?php if($detalle->equipo): ?>
                                    <small class="badge bg-info text-dark">
                                        <i class="bi bi-phone"></i> <?php echo e($detalle->equipo->serial_imei); ?>

                                    </small>
                                    <?php if($detalle->equipo->marca || $detalle->equipo->modelo): ?>
                                    <br><small class="text-muted">
                                        <?php echo e($detalle->equipo->marca); ?> <?php echo e($detalle->equipo->modelo); ?>

                                        <?php if($detalle->equipo->almacenamiento_gb): ?> · <?php echo e($detalle->equipo->almacenamiento_gb); ?>

                                        <?php endif; ?>
                                        <?php if($detalle->equipo->color): ?> · <?php echo e($detalle->equipo->color); ?>

                                        <?php endif; ?>
                                    </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <small class="text-muted">
                                        Stock actual: <strong><?php echo e($detalle->producto->stock); ?></strong>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold"><?php echo e($detalle->cantidad); ?></td>
                            <td class="text-end">RD$ <?php echo e(number_format($detalle->precio_unitario, 2)); ?></td>
                            <td class="text-end text-muted"><?php echo e(number_format($detalle->itbis_porcentaje ?? $systemItbis ?? 18, 2)); ?>%</td>
                            <td class="text-end text-muted">RD$ <?php echo e(number_format($detalle->base, 2)); ?></td>
                            <td class="text-end pe-4 fw-bold text-success">RD$ <?php echo e(number_format($detalle->subtotal, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay productos registrados.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot style="background:rgba(241,245,249,.5);">
                        <tr>
                            <td colspan="5" class="text-end fw-bold ps-4">Subtotal:</td>
                            <td class="text-end pe-4">RD$ <?php echo e(number_format($compra->subtotal ?? $compra->detalles->sum('base'), 2)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold ps-4">ITBIS:</td>
                            <td class="text-end pe-4">RD$ <?php echo e(number_format($compra->itbis_total ?? 0, 2)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold fs-5 ps-4">TOTAL:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-primary">RD$ <?php echo e(number_format($compra->total, 2)); ?></td>
                        </tr>
                        <?php if($compra->aplica_retencion_isr || $compra->aplica_retencion_itbis): ?>
                        <tr class="text-danger">
                            <td colspan="5" class="text-end fw-bold ps-4">Retención ISR:</td>
                            <td class="text-end pe-4">- RD$ <?php echo e(number_format($compra->retencion_isr, 2)); ?></td>
                        </tr>
                        <tr class="text-danger">
                            <td colspan="5" class="text-end fw-bold ps-4">Retención ITBIS:</td>
                            <td class="text-end pe-4">- RD$ <?php echo e(number_format($compra->retencion_itbis, 2)); ?></td>
                        </tr>
                        <tr class="text-success">
                            <td colspan="5" class="text-end fw-bold fs-5 ps-4">Total a Pagar:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-success">RD$ <?php echo e(number_format($compra->total_neto, 2)); ?></td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <?php if($compra->observaciones): ?>
    <div class="ui-card" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-chat-left-text me-2"></i>
            Observaciones
        </div>
        <div class="card-body">
            <p class="mb-0 text-muted"><?php echo e($compra->observaciones); ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/compras/show.blade.php ENDPATH**/ ?>