<?php $__env->startSection('title', 'Ganancias Repartidores'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.earning-stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#16a34a;--accent-rgb:22,163,74;--accent-hover:#15803d;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Ganancias Repartidores</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-graph-up me-1"></i>
                        <span>Control de ganancias por entrega y período</span>
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

    <?php if(session('error')): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-stat-icon" style="background:rgba(22,163,74,.1);color:#16a34a;">
                            <i class="bi bi-cash-coin"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Ganancias</div>
                    <div class="ui-stat-value" style="color:#16a34a;">RD$ <?php echo e(number_format($totalGanancias, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.15s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-stat-icon" style="background:rgba(14,165,233,.1);color:#0ea5e9;">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Propinas</div>
                    <div class="ui-stat-value" style="color:#0ea5e9;">RD$ <?php echo e(number_format($totalPropinas, 2)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;">
                            <i class="bi bi-box-seam"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Total Entregas</div>
                    <div class="ui-stat-value" style="color:#f59e0b;"><?php echo e($totalEntregas); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ui-card h-100" style="--delay:.25s">
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="earning-stat-icon" style="background:rgba(139,92,246,.1);color:#8b5cf6;">
                            <i class="bi bi-calendar-range"></i>
                        </span>
                    </div>
                    <div class="ui-stat-label">Períodos</div>
                    <div class="ui-stat-value" style="color:#8b5cf6;"><?php echo e($earnings->total()); ?></div>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($stats->count()): ?>
    <div class="ui-card mb-4" style="--delay:.3s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <h6 class="fw-bold mb-3" style="color:#16a34a;">
                <i class="bi bi-people me-2"></i>Resumen por Repartidor
            </h6>
            <div class="row g-3">
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $drv = $drivers->firstWhere('id', $stat->driver_id); ?>
                <?php if($drv): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="ui-card h-100" style="border:1px solid rgba(22,163,74,.15);">
                        <div class="ui-card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="driver-avatar" style="width:36px;height:36px;font-size:.75rem;background:linear-gradient(135deg,#16a34a,#15803d);">
                                    <?php echo e(strtoupper(substr($drv->nombre, 0, 1) . substr($drv->apellido, 0, 1))); ?>

                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size:.85rem;"><?php echo e($drv->nombre); ?> <?php echo e($drv->apellido); ?></div>
                                    <small class="text-muted"><?php echo e($drv->telefono ?? ''); ?></small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2" style="font-size:.78rem;">
                                <span class="text-muted"><?php echo e($stat->entregas); ?> entregas</span>
                                <span class="fw-bold" style="color:#16a34a;">RD$ <?php echo e(number_format($stat->ganancias + $stat->propinas, 2)); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="ui-card mb-4" style="--delay:.35s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('driver-earnings.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="ui-label">Repartidor</label>
                    <select name="driver_id" class="ui-select form-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($driver->id); ?>" <?php echo e(request('driver_id') == $driver->id ? 'selected' : ''); ?>>
                                <?php echo e($driver->nombre); ?> <?php echo e($driver->apellido); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Desde</label>
                    <input type="date" name="periodo_inicio" class="form-control form-control-sm rounded-3" value="<?php echo e(request('periodo_inicio')); ?>">
                </div>
                <div class="col-md-3">
                    <label class="ui-label">Hasta</label>
                    <input type="date" name="periodo_fin" class="form-control form-control-sm rounded-3" value="<?php echo e(request('periodo_fin')); ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="ui-btn ui-btn-solid w-100 rounded-pill">
                        <i class="bi bi-funnel me-1"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="ui-card" style="--delay:.4s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Período</th>
                        <th>Driver</th>
                        <th>Entregas</th>
                        <th>Ganancias</th>
                        <th>Propinas</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $earnings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $earning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-semibold"><?php echo e($earning->periodo_inicio->format('d/m/Y')); ?></span>
                            <span class="text-muted"> - <?php echo e($earning->periodo_fin->format('d/m/Y')); ?></span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="driver-avatar" style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg,#16a34a,#15803d);">
                                    <?php echo e(strtoupper(substr($earning->driver->nombre, 0, 1) . substr($earning->driver->apellido, 0, 1))); ?>

                                </div>
                                <small><?php echo e($earning->driver->nombre); ?> <?php echo e($earning->driver->apellido); ?></small>
                            </div>
                        </td>
                        <td><?php echo e($earning->total_entregas); ?></td>
                        <td class="fw-bold" style="color:#16a34a;">RD$ <?php echo e(number_format($earning->total_ganancias, 2)); ?></td>
                        <td style="color:#0ea5e9;">RD$ <?php echo e(number_format($earning->propinas ?? 0, 2)); ?></td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('driver-earnings.show', $earning)); ?>" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay períodos de ganancias registrados
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($earnings->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 p-3">
            <?php echo e($earnings->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/driver-earnings/index.blade.php ENDPATH**/ ?>