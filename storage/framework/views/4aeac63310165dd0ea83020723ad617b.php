<?php $__env->startSection('title', 'Planes'); ?>

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
                    <i class="bi bi-card-checklist"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Planes de Suscripci&oacute;n</h2>
                    <p class="mb-0 opacity-75">Gesti&oacute;n de los planes mensuales vendibles</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.plans.create')); ?>" class="ui-btn ui-btn-solid">
                    <i class="bi bi-plus-lg me-2"></i>Nuevo Plan
                </a>
                <a href="<?php echo e(route('owner.dashboard')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver al Panel
                </a>
            </div>
        </div>
    </div>

    <?php
        $stats = [
            'planes'   => $planes->count(),
            'activos'  => $planes->where('activo', true)->count(),
            'recomendados' => $planes->where('recomendado', true)->count(),
            'instancias' => $planes->sum('business_instances_count'),
        ];
    ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#8b5cf6">
                        <i class="bi bi-card-checklist fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['planes']); ?></div>
                        <small class="text-muted">Planes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.2s">
                <div class="ui-card-accent" style="background:#22c55e"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#22c55e">
                        <i class="bi bi-check-circle fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['activos']); ?></div>
                        <small class="text-muted">Activos</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.3s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:46px;height:46px;background:#f59e0b">
                        <i class="bi bi-star fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1"><?php echo e($stats['recomendados']); ?></div>
                        <small class="text-muted">Recomendados</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
            <div class="ui-card h-100" style="--delay:.4s">
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
    </div>

    <div class="row g-3">
        <?php $__empty_0 = true; $__currentLoopData = $planes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100" style="--delay:.<?php echo e(min(5, $loop->iteration)); ?>s">
                <?php if($plan->recomendado): ?>
                    <div class="ui-card-accent" style="background:#8b5cf6"></div>
                <?php else: ?>
                    <div class="ui-card-accent" style="background:#3b82f6"></div>
                <?php endif; ?>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width:52px;height:52px;background:<?php echo e($plan->recomendado ? '#8b5cf6' : '#3b82f6'); ?>;">
                            <i class="bi bi-<?php echo e($plan->recomendado ? 'star-fill' : 'card-checklist'); ?> fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-0">
                                <?php echo e($plan->nombre); ?>

                                <?php if($plan->recomendado): ?>
                                    <span class="ui-badge ui-badge-primary rounded-pill ms-1" style="font-size:.55rem;">Recomendado</span>
                                <?php endif; ?>
                                <?php if(!$plan->activo): ?>
                                    <span class="ui-badge ui-badge-neutral rounded-pill ms-1" style="font-size:.55rem;">Inactivo</span>
                                <?php endif; ?>
                            </h5>
                            <small class="text-muted"><?php echo e($plan->slug); ?></small>
                        </div>
                    </div>

                    <?php if($plan->descripcion): ?>
                        <p class="text-muted small mb-3"><?php echo e($plan->descripcion); ?></p>
                    <?php endif; ?>

                    <div class="mb-3">
                        <span class="fs-3 fw-bold" style="color:var(--accent);">RD$ <?php echo e(number_format($plan->precio_mensual, 2)); ?></span>
                        <span class="text-muted small">/mes</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-primary"><?php echo e($plan->max_usuarios === null ? '&infin;' : $plan->max_usuarios); ?></div>
                                <small class="text-muted">Usuarios</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-success"><?php echo e($plan->max_sucursales === null ? '&infin;' : $plan->max_sucursales); ?></div>
                                <small class="text-muted">Sucursales</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="rounded-3 border p-2 text-center">
                                <div class="fs-5 fw-bold text-info"><?php echo e($plan->business_instances_count); ?></div>
                                <small class="text-muted">Instancias</small>
                            </div>
                        </div>
                    </div>

                    <?php if(!empty($plan->features)): ?>
                    <div class="mb-3">
                        <small class="fw-bold text-muted d-block mb-2">Features (<?php echo e(count($plan->features)); ?>)</small>
                        <div class="d-flex flex-wrap gap-1">
                            <?php $__currentLoopData = array_slice($plan->features, 0, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="ui-badge ui-badge-neutral rounded-pill px-2 py-1" style="font-size:.65rem;">
                                    <?php echo e($feature); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(count($plan->features) > 4): ?>
                                <span class="ui-badge ui-badge-neutral rounded-pill px-2 py-1" style="font-size:.65rem;">
                                    +<?php echo e(count($plan->features) - 4); ?> m&aacute;s
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('owner.plans.edit', $plan)); ?>" class="ui-btn ui-btn-ghost rounded-pill flex-grow-1">
                            <i class="bi bi-pencil me-2"></i>Editar
                        </a>
                        <form method="POST" action="<?php echo e(route('owner.plans.destroy', $plan)); ?>" onsubmit="return UI.confirm.delete('&iquest;Eliminar el plan "<?php echo e($plan->nombre); ?>"?')" class="d-inline">
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
                    <p class="mt-2 mb-0">No hay planes creados. <a href="<?php echo e(route('owner.plans.create')); ?>">Crea el primero</a>.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/planes/index.blade.php ENDPATH**/ ?>