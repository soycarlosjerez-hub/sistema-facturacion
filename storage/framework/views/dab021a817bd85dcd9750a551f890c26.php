<?php $__env->startSection('title', 'Devolución ' . $devolucion->codigo); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.devoluciones-show-table {
    --bs-table-bg: transparent;
    margin: 0;
}
.devoluciones-show-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .85rem 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.devoluciones-show-table tbody td {
    padding: .85rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .9rem;
}
.devoluciones-show-table tbody tr:last-child td { border-bottom: none; }
body.dark-mode .devoluciones-show-table thead th {
    background: rgba(15,23,42,.5);
    color: #94a3b8;
    border-color: #1e293b;
}
body.dark-mode .devoluciones-show-table tbody td {
    border-bottom-color: #1e293b;
    color: #cbd5e1;
}
</style>
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
                    <i class="bi bi-arrow-return-left"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Devolución <?php echo e($devolucion->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-arrow-return-left me-1"></i>
                        <span>Detalle completo de la devolución</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('devoluciones.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('devoluciones.create')): ?>
                <a href="<?php echo e(route('devoluciones.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success border-0 rounded-3 mb-3"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger border-0 rounded-3 mb-3"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label mb-1">Código</div>
                    <div class="ui-stat-value text-dark"><?php echo e($devolucion->codigo); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label mb-1">Cliente</div>
                    <div class="ui-stat-value" style="font-size:1.1rem;color:#1e293b;"><?php echo e($devolucion->cliente?->nombre ?? 'N/A'); ?></div>
                    <div class="ui-stat-sub"><?php echo e($devolucion->cliente?->rnc_cedula ?? ''); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label mb-1">Fecha / Tipo</div>
                    <div class="ui-stat-value" style="font-size:1.1rem;color:#1e293b;"><?php echo e($devolucion->fecha?->format('d/m/Y')); ?></div>
                    <span class="ui-badge ui-badge-info rounded-pill mt-1"><?php echo e(ucfirst($devolucion->tipo)); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat h-100" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body">
                    <div class="ui-stat-label mb-1">Total Devuelto</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($devolucion->total, 2)); ?></div>
                    <div class="ui-stat-sub">Incluye ITBIS</div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-box-seam"></i>
            Productos Devueltos
            <?php
                $estadoInfo = match($devolucion->estado) {
                    'borrador' => ['warning', 'clock', 'Borrador'],
                    'completada' => ['success', 'check-circle', 'Completada'],
                    'anulada' => ['danger', 'x-circle', 'Anulada'],
                    default => ['secondary', 'circle', $devolucion->estado],
                };
            ?>
        </div>
        <div class="ui-card-subtitle">
            <span class="ui-badge ui-badge-<?php echo e($estadoInfo[0]); ?> rounded-pill px-3">
                <i class="bi bi-<?php echo e($estadoInfo[1]); ?> me-1"></i><?php echo e($estadoInfo[2]); ?>

            </span>
        </div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="table devoluciones-show-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">ITBIS</th>
                            <th class="text-end pe-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $devolucion->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="ps-4 fw-bold"><?php echo e($item->producto?->nombre ?? 'N/A'); ?></td>
                            <td class="text-end"><?php echo e($item->cantidad); ?></td>
                            <td class="text-end">RD$ <?php echo e(number_format($item->precio_unitario, 2)); ?></td>
                            <td class="text-end"><?php echo e(number_format($item->itbis_porcentaje, 2)); ?>%</td>
                            <td class="text-end pe-4 fw-bold">RD$ <?php echo e(number_format($item->subtotal, 2)); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot class="bg-light bg-opacity-50">
                        <tr>
                            <td colspan="4" class="text-end fw-bold ps-4">Subtotal:</td>
                            <td class="text-end pe-4">RD$ <?php echo e(number_format($devolucion->subtotal, 2)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold ps-4">ITBIS:</td>
                            <td class="text-end pe-4">RD$ <?php echo e(number_format($devolucion->itbis, 2)); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5 ps-4">TOTAL:</td>
                            <td class="text-end pe-4 fw-bold fs-5 text-primary">RD$ <?php echo e(number_format($devolucion->total, 2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-chat-left-text"></i>
            Motivo
        </div>
        <div class="ui-card-body">
            <p class="mb-0 text-muted"><?php echo e($devolucion->motivo); ?></p>
            <?php if($devolucion->venta): ?>
            <hr>
            <small class="text-muted">
                <i class="bi bi-receipt me-1"></i>Venta asociada:
                <a href="<?php echo e(route('ventas.show', $devolucion->venta)); ?>" class="text-decoration-none fw-bold">#<?php echo e(str_pad($devolucion->venta_id, 5, '0', STR_PAD_LEFT)); ?></a>
                &middot; <?php echo e($devolucion->user?->name ?? 'N/A'); ?>

            </small>
            <?php endif; ?>
        </div>
    </div>

    <?php if($devolucion->notaCredito): ?>
    <div class="ui-card mb-4" style="--delay:.4s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-title">
            <i class="bi bi-check-circle-fill"></i>
            Nota de Crédito Electrónica Generada
        </div>
        <div class="ui-card-body">
            <div class="d-flex align-items-center">
                <div>
                    <a href="<?php echo e(route('ecf.show', $devolucion->notaCredito)); ?>" class="text-decoration-none">
                        <?php echo e($devolucion->notaCredito->encf); ?>

                        <span class="ui-badge ui-badge-<?php echo e($devolucion->notaCredito->estado_info['color']); ?> ms-2"><?php echo e($devolucion->notaCredito->estado_info['label']); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex gap-2 justify-content-end mb-4">
        <?php if($devolucion->estado === 'borrador'): ?>
            <form action="<?php echo e(route('devoluciones.confirmar', $devolucion)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="ui-btn ui-btn-solid rounded-pill px-4" onclick="confirmAction({title:'Confirmar Devolución', text:'¿Confirmar devolución? El stock será reintegrado al inventario.', icon:'info', color:'#3b82f6', confirmText:'Confirmar', onSubmit:function(){ this.closest('form').submit(); }})">
                    <i class="bi bi-check-lg me-2"></i>Confirmar y Reintegrar Stock
                </button>
            </form>
        <?php endif; ?>
        <?php if($devolucion->estado === 'completada' && $devolucion->tiene_ecf && !$devolucion->nota_credito_id): ?>
            <form action="<?php echo e(route('devoluciones.generar-nc', $devolucion)); ?>" method="POST" class="d-inline">
                <?php echo csrf_field(); ?>
                <button class="ui-btn ui-btn-ghost rounded-pill px-4">
                    <i class="bi bi-file-earmark-minus me-2"></i>Generar Nota de Crédito E34
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/devoluciones/show.blade.php ENDPATH**/ ?>