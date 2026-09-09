<?php $__env->startSection('title', 'Exhibiciones'); ?>
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
                    <i class="bi bi-easel"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-calendar-event me-1"></i>EVENTOS
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Exhibiciones</h2>
                    <p class="mb-0 opacity-75">Exhibiciones y eventos de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.exhibiciones.create')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Exhibición
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="GET" action="<?php echo e(route('arte.exhibiciones.index')); ?>">
                <div class="ui-input-group">
                    <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="ui-input" value="<?php echo e(request('q')); ?>" placeholder="Buscar por nombre o ubicación...">
                    <button class="ui-btn ui-btn-solid rounded-pill ms-2 px-4" type="submit">Buscar</button>
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
                            <th>Exhibición</th>
                            <th>Ubicación</th>
                            <th>Fechas</th>
                            <th class="text-center">Obras</th>
                            <th class="text-center">Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_0 = true; $__currentLoopData = $exhibiciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <tr>
                            <td class="ps-4"><?php echo e($e->id); ?></td>
                            <td class="fw-semibold"><?php echo e($e->nombre); ?></td>
                            <td><?php echo e($e->ubicacion ?? '—'); ?></td>
                            <td class="small"><?php echo e($e->rango_fechas); ?></td>
                            <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill"><?php echo e($e->obras_count); ?></span></td>
                            <td class="text-center">
                                <span class="badge <?php echo e($e->activa ? 'bg-success' : 'bg-secondary'); ?> rounded-pill"><?php echo e($e->activa ? 'Activa' : 'Inactiva'); ?></span>
                            </td>
                            <td class="text-end text-nowrap pe-4">
                                <a href="<?php echo e(route('arte.exhibiciones.show', $e)); ?>" class="ui-action ui-action-view" title="Ver"><i class="bi bi-eye"></i></a>
                                <a href="<?php echo e(route('arte.exhibiciones.edit', $e)); ?>" class="ui-action ui-action-edit" title="Editar"><i class="bi bi-pencil"></i></a>
                                <form action="<?php echo e(route('arte.exhibiciones.destroy', $e)); ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar la exhibición <?php echo e(addslashes($e->nombre)); ?>?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="ui-action ui-action-delete" title="Eliminar"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No hay exhibiciones registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-3"><?php echo e($exhibiciones->links()); ?></div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/exhibiciones/index.blade.php ENDPATH**/ ?>