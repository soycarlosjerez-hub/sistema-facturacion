<?php $__env->startSection('title', 'Seguimiento de Entregas'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
/* Tracking Module Styles */
.timeline-container {
    position: relative;
    padding-left: 2rem;
}
.timeline-container::before {
    content: '';
    position: absolute;
    left: 14px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-item:last-child { padding-bottom: 0; }
.timeline-dot {
    position: absolute;
    left: -2rem;
    top: 2px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid;
    background: #fff;
    z-index: 1;
}
.timeline-dot.completed { background: #16a34a; border-color: #16a34a; }
.timeline-dot.current { background: #0ea5e9; border-color: #0ea5e9; animation: uiPulse 2s infinite; }
.timeline-dot.pending { background: #fff; border-color: #cbd5e1; }
.timeline-dot.failed { background: #dc2626; border-color: #dc2626; }
@keyframes uiPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(14,165,233,.4); }
    50% { box-shadow: 0 0 0 6px rgba(14,165,233,0); }
}
.timeline-time {
    font-size: .75rem;
    color: #94a3b8;
    font-weight: 500;
}
.timeline-title {
    font-weight: 600;
    font-size: .9rem;
    color: #1e293b;
}
.timeline-desc {
    font-size: .82rem;
    color: #64748b;
    margin-top: .15rem;
}
.filter-chip {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .4rem .85rem;
    border-radius: 9999px;
    font-size: .8rem;
    font-weight: 600;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
    cursor: pointer;
    transition: all .2s ease;
}
.filter-chip:hover { border-color: #0ea5e9; color: #0ea5e9; }
.filter-chip.active { background: rgba(14,165,233,.1); border-color: #0ea5e9; color: #0ea5e9; }
.map-placeholder {
    background: #f1f5f9;
    border-radius: var(--radius-lg);
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    color: #94a3b8;
    border: 2px dashed #e2e8f0;
}
.map-placeholder i { font-size: 2.5rem; margin-bottom: .5rem; }
@media (max-width: 767.98px) {
    .timeline-container { padding-left: 1.5rem; }
    .timeline-dot { left: -1.5rem; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-geo-alt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Seguimiento de Entregas</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-signpost-split me-1"></i>
                        <span>Monitoreo en tiempo real de entregas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-check-circle me-2"></i><?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('delivery-tracking.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="ui-label">Orden</label>
                    <select name="order_id" class="ui-select form-select">
                        <option value="">Todas las órdenes</option>
                        <?php $__currentLoopData = $orders ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($order->id); ?>" <?php echo e(request('order_id') == $order->id ? 'selected' : ''); ?>>
                            #<?php echo e($order->id); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Repartidor</label>
                    <select name="driver_id" class="ui-select form-select">
                        <option value="">Todos los repartidores</option>
                        <?php $__currentLoopData = $drivers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($driver->id); ?>" <?php echo e(request('driver_id') == $driver->id ? 'selected' : ''); ?>>
                            <?php echo e($driver->nombre); ?> <?php echo e($driver->apellido); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Estado</label>
                    <select name="status" class="ui-select form-select">
                        <option value="">Todos</option>
                        <option value="creado" <?php echo e(request('status') == 'creado' ? 'selected' : ''); ?>>Creado</option>
                        <option value="en_camino" <?php echo e(request('status') == 'en_camino' ? 'selected' : ''); ?>>En Camino</option>
                        <option value="entregado" <?php echo e(request('status') == 'entregado' ? 'selected' : ''); ?>>Entregado</option>
                        <option value="fallido" <?php echo e(request('status') == 'fallido' ? 'selected' : ''); ?>>Fallido</option>
                        <option value="cancelado" <?php echo e(request('status') == 'cancelado' ? 'selected' : ''); ?>>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Orden #</th>
                        <th>Cliente</th>
                        <th>Driver</th>
                        <th>Status</th>
                        <th>Fecha Creación</th>
                        <th>Tiempo Est.</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $trackings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4 fw-semibold">
                            <?php if($t->deliveryTracking): ?>
                            <a href="<?php echo e(route('delivery-tracking.show', $t->deliveryTracking)); ?>" class="text-decoration-none" style="color:#0ea5e9;">
                                #<?php echo e($t->orden->id ?? $t->id); ?>

                            </a>
                            <?php else: ?>
                            <a href="<?php echo e(route('ventas.show', $t)); ?>" class="text-decoration-none" style="color:#0ea5e9;">
                                #<?php echo e($t->orden->id ?? $t->id); ?>

                            </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="small">
                                <div class="fw-medium"><?php echo e($t->cliente?->nombre ?? 'N/A'); ?></div>
                                <small class="text-muted"><?php echo e(Str::limit($t->delivery_address ?? '', 30)); ?></small>
                            </div>
                        </td>
                        <td>
                            <?php if($t->driver): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="driver-avatar" style="width:32px;height:32px;font-size:.7rem;">
                                        <?php echo e(strtoupper(substr($t->driver->nombre, 0, 1) . substr($t->driver->apellido, 0, 1))); ?>

                                    </div>
                                    <small><?php echo e($t->driver->nombre); ?> <?php echo e($t->driver->apellido); ?></small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                $statusConfig = [
                                    'creado'       => ['badge' => 'neutral', 'icon' => 'bi-clock', 'label' => 'Creado'],
                                    'en_camino'    => ['badge' => 'info',    'icon' => 'bi-truck', 'label' => 'En Camino'],
                                    'entregado'    => ['badge' => 'success', 'icon' => 'bi-check-circle', 'label' => 'Entregado'],
                                    'fallido'      => ['badge' => 'danger',  'icon' => 'bi-x-circle', 'label' => 'Fallido'],
                                    'cancelado'    => ['badge' => 'warning', 'icon' => 'bi-slash-circle', 'label' => 'Cancelado'],
                                ];
                                $cfg = $statusConfig[$t->_virtual_status ?? 'creado'] ?? $statusConfig['creado'];
                            ?>
                            <span class="ui-badge ui-badge-<?php echo e($cfg['badge']); ?>">
                                <i class="bi <?php echo e($cfg['icon']); ?> me-1"></i><?php echo e($cfg['label']); ?>

                            </span>
                        </td>
                        <td>
                            <small class="text-muted"><?php echo e($t->created_at->format('d/m/Y H:i')); ?></small>
                        </td>
                        <td>
                            <?php if($t->deliveryZone?->tiempo_estimado_minutos): ?>
                                <span class="small"><i class="bi bi-clock me-1"></i><?php echo e($t->deliveryZone->tiempo_estimado_minutos); ?> min</span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <?php if($t->deliveryTracking): ?>
                            <a href="<?php echo e(route('delivery-tracking.show', $t->deliveryTracking)); ?>" class="ui-action ui-action-view" title="Ver seguimiento">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php else: ?>
                            <a href="<?php echo e(route('ventas.show', $t)); ?>" class="ui-action ui-action-view" title="Ver venta">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-signpost-2 fs-1 d-block mb-2"></i>
                            No hay entregas registradas
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($trackings->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 p-3">
            <?php echo e($trackings->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-tracking/index.blade.php ENDPATH**/ ?>