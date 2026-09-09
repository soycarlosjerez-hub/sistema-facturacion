<?php $__env->startSection('title', 'Módulos del Sistema'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">

<div class="ui-header mb-4" style="--delay:0s">
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="bubble"></div>
    <div class="ui-header-body">
        <div class="ui-header-left">
            <div class="ui-avatar-circle">
                <i class="bi bi-grid"></i>
            </div>
            <div>
                <h4 class="ui-header-title">Módulos del Sistema</h4>
                <div class="ui-header-meta">
                    <i class="bi bi-box-seam me-1"></i>Gestiona los módulos disponibles para asignar a Tipos de Negocio y Roles de Instancia.
                </div>
            </div>
        </div>
        <div class="ui-header-actions">
            <a href="<?php echo e(route('owner.modules.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Nuevo Módulo
            </a>
            <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
</div>

<?php if(session('success')): ?>
    <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php $__currentLoopData = $modulos->groupBy('categoria'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria => $modulosCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="ui-card mb-4" style="--delay:.1s">
    <div class="ui-card-accent"></div>
    <div class="ui-card-title">
        <i class="bi bi-folder2-open"></i><?php echo e(ucfirst($categoria)); ?>

    </div>
    <div class="ui-card-subtitle"><?php echo e($modulosCat->count()); ?> módulo(s)</div>
    <div class="ui-card-body p-0">
        <div class="table-responsive">
            <table class="ui-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Módulo</th>
                        <th>Key</th>
                        <th>Icono</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $modulosCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="ps-4 fw-bold">
                            <i class="bi <?php echo e($modulo->icon ?? 'bi-circle'); ?> me-2" style="color:var(--accent)"></i>
                            <?php echo e($modulo->label); ?>

                        </td>
                        <td><code><?php echo e($modulo->key); ?></code></td>
                        <td><code><?php echo e($modulo->icon); ?></code></td>
                        <td><?php echo e($modulo->orden); ?></td>
                        <td>
                            <?php if($modulo->activo): ?>
                                <span class="ui-badge ui-badge-success">
                                    <i class="bi bi-check-circle me-1"></i>Activo
                                </span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-neutral">
                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('owner.modules.edit', $modulo)); ?>" class="ui-action ui-action-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if($modulo->activo): ?>
                            <form method="POST" action="<?php echo e(route('owner.modules.destroy', $modulo)); ?>" class="d-inline" onsubmit="return confirm('¿Desactivar el módulo <?php echo e($modulo->label); ?>?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="ui-action ui-action-delete" title="Desactivar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/modules/index.blade.php ENDPATH**/ ?>