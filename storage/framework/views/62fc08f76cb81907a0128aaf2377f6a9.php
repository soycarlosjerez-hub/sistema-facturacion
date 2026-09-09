<?php $__env->startSection('title', 'Tipos de Negocio'); ?>

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
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Tipos de Negocio</h2>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de tipos de negocio y sus m&oacute;dulos disponibles.</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.business-types.create')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo
                </a>
                <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#8b5cf6">
                        <i class="bi bi-tags fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['tipos']); ?></div>
                        <small class="text-muted">Tipos de Negocio</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#0ea5e9"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#0ea5e9">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['instancias']); ?></div>
                        <small class="text-muted">Instancias</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#22c55e"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#22c55e">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['activas']); ?></div>
                        <small class="text-muted">Activas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.4s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#f59e0b">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['usuarios']); ?></div>
                        <small class="text-muted">Usuarios</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.5s">
                <div class="ui-card-accent" style="background:#ec4899"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#ec4899">
                        <i class="bi bi-grid-1x2 fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['modulos']); ?></div>
                        <small class="text-muted">M&oacute;dulos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php $__empty_0 = true; $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.<?php echo e(min(5, $loop->iteration)); ?>s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:52px;height:52px;background-color:var(--bs-<?php echo e($type->color ?? 'secondary'); ?>);">
                            <i class="<?php echo e($type->icon ?? 'bi-grid'); ?> fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                <?php echo e($type->nombre); ?>

                                <?php if(!$type->activo): ?>
                                    <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">Inactivo</span>
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted">Slug: <?php echo e($type->slug); ?></small>
                        </div>
                    </div>
                    <p class="text-muted small mb-3"><?php echo e($type->descripcion ?? 'Sin descripci&oacute;n'); ?></p>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-primary"><?php echo e($type->business_instances_count); ?></div>
                                <small class="text-muted">Instancias</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-success"><?php echo e($type->users_asociados_count); ?></div>
                                <small class="text-muted">Usuarios</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-info"><?php echo e($type->instancias_activas); ?></div>
                                <small class="text-muted">Activas</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="fw-bold text-muted d-block mb-2">M&oacute;dulos visibles (<?php echo e($type->modules->where('visible', true)->count()); ?>/<?php echo e($type->modules->count()); ?>)</small>
                        <div class="d-flex flex-wrap gap-1">
                            <?php $__currentLoopData = $type->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="ui-badge rounded-pill px-2 py-1 <?php echo e($module->visible ? 'ui-badge-success' : 'ui-badge-neutral'); ?>" style="font-size:.65rem;">
                                    <?php echo e($module->modulo_key); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('owner.business-types.edit', $type)); ?>" class="ui-btn ui-btn-ghost rounded-pill flex-grow-1">
                            <i class="bi bi-pencil me-2"></i>Editar M&oacute;dulos
                        </a>
                        <form method="POST" action="<?php echo e(route('owner.business-types.destroy', $type)); ?>" onsubmit="return UI.confirm.delete('&iquest;Eliminar el tipo de negocio &quot;<?php echo e($type->nombre); ?>&quot;? Esta acci&oacute;n no se puede deshacer.')" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="ui-action ui-action-delete" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
        <div class="col-12">
            <div class="ui-card" style="--delay:.1s">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2 mb-0">No hay tipos de negocio registrados.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/business-types/index.blade.php ENDPATH**/ ?>