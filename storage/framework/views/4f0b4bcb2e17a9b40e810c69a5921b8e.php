<?php $__env->startSection('title', 'Vehículos'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-car-front-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Vehículos</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-card-list me-1"></i>
                        <span>Registro de vehículos y su historial de servicios</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <form method="GET" class="d-flex gap-2">
                    <input type="text" name="q" class="ui-input" placeholder="Buscar placa, marca, cliente..." value="<?php echo e(request('q')); ?>">
                    <button class="ui-btn ui-btn-solid"><i class="bi bi-search me-1"></i> Buscar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table mb-0">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Marca / Modelo</th>
                            <th>Año</th>
                            <th>Color</th>
                            <th>Cliente</th>
                            <th>Visitas</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $vehiculos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($v->placa ?? '—'); ?></td>
                            <td><?php echo e($v->marca); ?> <?php echo e($v->modelo); ?></td>
                            <td><?php echo e($v->anio ?? '—'); ?></td>
                            <td><?php echo e($v->color ?? '—'); ?></td>
                            <td><?php echo e($v->cliente?->nombre ?? '—'); ?></td>
                            <td><?php echo e($v->ventas_count ?? 0); ?></td>
                            <td class="text-end">
                                <a href="<?php echo e(route('lavadero.vehiculos.show', $v)); ?>" class="ui-action ui-action-view" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr>
                            <td colspan="7">
                                <div class="ui-empty-state">
                                    <i class="bi bi-car-front"></i>
                                    <p>Sin vehículos registrados</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3"><?php echo e($vehiculos->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/lavadero/vehiculos/index.blade.php ENDPATH**/ ?>