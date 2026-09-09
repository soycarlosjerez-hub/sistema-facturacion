<?php $__env->startSection('title', 'Detalle del Cliente'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.info-item {
    background: #f8fafc;
    border-radius: 0.75rem;
    padding: 1rem;
    border-left: 3px solid #10b981;
}
.info-item .label {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    font-weight: 700;
    margin-bottom: 4px;
}
.info-item .value {
    font-weight: 600;
    color: #1e293b;
}
.venta-card {
    background: white;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
    transition: all 0.2s;
}
.venta-card:hover {
    border-color: #10b981;
    box-shadow: 0 4px 12px rgba(16,185,129,0.1);
}
.stat-badge {
    background: rgba(16,185,129,0.1);
    color: #059669;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 700;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
body.dark-mode .info-item { background: rgba(30,41,59,.8); }
body.dark-mode .info-item .label { color: #94a3b8; }
body.dark-mode .info-item .value { color: #f1f5f9; }
body.dark-mode .venta-card { background: #0f172a; border-color: #1e293b; }
body.dark-mode .venta-card:hover { border-color: #10b981; }
body.dark-mode .stat-badge { background: rgba(16,185,129,0.15); color: #4ade80; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($cliente->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-person me-1"></i>
                        <?php echo e($cliente->tipo_cliente_label); ?> · <?php echo e($cliente->segmento_label); ?>

                        <?php if($cliente->rnc): ?>
                            <span class="divider">·</span>
                            <i class="bi bi-upc-scan me-1"></i><?php echo e($cliente->rnc); ?>

                        <?php endif; ?>
                        <span class="divider">·</span>
                        <span class="ui-badge ui-badge-<?php echo e($cliente->activo ? 'success' : 'danger'); ?>"><?php echo e($cliente->activo_label); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('clientes.edit')): ?>
                <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('clientes.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Límite Crédito</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($cliente->limite_credito, 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Balance Pendiente</div>
                    <div class="ui-stat-value" style="color:<?php echo e($cliente->balance_pendiente > 0 ? '#ef4444' : '#10b981'); ?>;">RD$ <?php echo e(number_format($cliente->balance_pendiente, 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Compras Totales</div>
                    <div class="ui-stat-value">RD$ <?php echo e(number_format($stats->total_compras ?? 0, 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-stat-body text-center">
                    <div class="ui-stat-label">Días de Crédito</div>
                    <div class="ui-stat-value"><?php echo e($cliente->dias_credito ?: 0); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-info-circle"></i> Información General</div>
                <div class="ui-card-body">
                    <div class="info-item mb-3">
                        <div class="label">Teléfono</div>
                        <div class="value"><?php echo e($cliente->telefono ?? '—'); ?></div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Email</div>
                        <div class="value"><?php echo e($cliente->email ?? '—'); ?></div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Contacto</div>
                        <div class="value"><?php echo e($cliente->persona_contacto ?? '—'); ?></div>
                    </div>
                    <div class="info-item mb-3">
                        <div class="label">Dirección</div>
                        <div class="value"><?php echo e($cliente->direccion ?? '—'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Ciudad</div>
                        <div class="value"><?php echo e($cliente->ciudad ?? '—'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title"><i class="bi bi-receipt"></i> Últimas Ventas</div>
                <div class="ui-card-body">
                    <?php if(isset($ultimasVentas) && $ultimasVentas->count() > 0): ?>
                        <?php $__currentLoopData = $ultimasVentas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="venta-card mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">#<?php echo e(str_pad($vta->id, 5, '0', STR_PAD_LEFT)); ?></span>
                                    <span class="text-muted mx-2">·</span>
                                    <span class="text-muted small"><?php echo e($vta->created_at->format('d/m/Y h:i A')); ?></span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold">RD$ <?php echo e(number_format($vta->total, 2)); ?></span>
                                    <span class="ui-badge ui-badge-<?php echo e($vta->estado === 'completada' ? 'success' : ($vta->estado === 'pendiente' ? 'warning' : 'danger')); ?>">
                                        <?php echo e(ucfirst($vta->estado)); ?>

                                    </span>
                                    <a href="<?php echo e(route('ventas.show', $vta)); ?>" class="ui-action ui-action-view"><i class="bi bi-eye"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="ui-empty-state">
                            <i class="bi bi-receipt"></i>
                            <p>Sin ventas registradas</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/clientes/show.blade.php ENDPATH**/ ?>