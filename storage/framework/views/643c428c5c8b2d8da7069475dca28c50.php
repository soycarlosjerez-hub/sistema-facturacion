<?php $__env->startSection('title', 'Empresas de Delivery'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-truck"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Empresas de Delivery</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span><?php echo e($companies->total()); ?> registro(s)</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delivery-companies.create')): ?>
                <a href="<?php echo e(route('delivery-companies.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nueva Empresa
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success rounded-4 shadow-sm border-0 mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Nombre</th>
                        <th>Código</th>
                        <th>Comisión</th>
                        <th>Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <tr>
                        <td class="ps-4 fw-semibold"><?php echo e($company->nombre); ?></td>
                        <td><span class="ui-badge ui-badge-neutral"><?php echo e($company->nombre_corto); ?></span></td>
                        <td><?php echo e($company->comision_formateada); ?></td>
                        <td>
                            <?php if($company->activo): ?>
                                <span class="ui-badge ui-badge-success">Activo</span>
                            <?php else: ?>
                                <span class="ui-badge ui-badge-neutral">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('delivery-companies.edit', $company)); ?>" class="ui-action ui-action-edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?php echo e(route('delivery-companies.destroy', $company)); ?>" method="POST" class="d-inline" onsubmit="return UI.confirm.delete('¿Eliminar esta empresa? Las ventas asociadas no se verán afectadas.')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="ui-action ui-action-delete" type="submit">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            No hay empresas de delivery registradas
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light border-0 p-3">
            <?php echo e($companies->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-companies/index.blade.php ENDPATH**/ ?>