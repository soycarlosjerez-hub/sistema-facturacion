<?php $__env->startSection('title', 'Consignaciones'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-arrow-left-right me-1"></i>TERCEROS
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Consignaciones</h2>
                    <p class="mb-0 opacity-75">Obras en consignación de terceros</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.consignaciones.create')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Consignación
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('arte.consignaciones.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="ui-label" for="q">Buscar</label>
                    <input type="text" name="q" id="q" class="ui-input" value="<?php echo e(request('q')); ?>" placeholder="Consignante u obra...">
                </div>
                <div class="col-md-3">
                    <label class="ui-label" for="estado">Estado</label>
                    <select name="estado" id="estado" class="ui-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = ['activa' => 'Activa', 'completada' => 'Completada', 'cancelada' => 'Cancelada']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($k); ?>" <?php echo e(request('estado') == $k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="ui-btn ui-btn-solid rounded-pill w-100" type="submit" title="Filtrar"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body p-0">
            <div class="table-responsive">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Obra</th>
                            <th>Consignante</th>
                            <th class="text-end">Comisión</th>
                            <th>Inicio</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $consignaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4"><?php echo e($c->id); ?></td>
                            <td class="fw-semibold"><?php echo e($c->obra?->titulo ?? '—'); ?></td>
                            <td><?php echo e($c->consignante); ?></td>
                            <td class="text-end"><?php echo e($c->porcentaje_comision); ?>%</td>
                            <td class="small"><?php echo e(optional($c->fecha_inicio)->format('d/m/Y')); ?></td>
                            <td><span class="badge bg-<?php echo e($c->estado_badge_class); ?> rounded-pill"><?php echo e($c->estado_label); ?></span></td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="<?php echo e(route('arte.consignaciones.edit', $c)); ?>" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('arte.consignaciones.destroy', $c)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta consignación?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay consignaciones registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3"><?php echo e($consignaciones->links()); ?></div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/consignaciones/index.blade.php ENDPATH**/ ?>