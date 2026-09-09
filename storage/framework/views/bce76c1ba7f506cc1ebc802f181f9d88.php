<?php $__env->startSection('title', 'Ver Especialidad Técnica'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($tecnicaEspecialidad->nombre); ?></h4>
                    <div class="ui-header-meta">Detalles de la especialidad técnica</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('tecnica-especialidades.edit')): ?>
                <a href="<?php echo e(route('tecnica-especialidades.edit', $tecnicaEspecialidad)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('tecnica-especialidades.index')); ?>" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información General</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong><?php echo e($tecnicaEspecialidad->nombre); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Descripción</small>
                        <p class="text-muted"><?php echo e($tecnicaEspecialidad->descripcion ?? 'Sin descripción'); ?></p>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden de Visualización</small>
                        <strong><?php echo e($tecnicaEspecialidad->orden); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge <?php echo e($tecnicaEspecialidad->activo ? 'bg-success' : 'bg-secondary'); ?> fs-6">
                            <?php echo e($tecnicaEspecialidad->activo_label); ?>

                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Técnicos Asignados</small>
                        <span class="badge bg-info fs-6"><?php echo e($tecnicaEspecialidad->tecnicos_count ?? 0); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="fw-bold mb-0">Técnicos con esta Especialidad</h6>
                </div>
                <div class="card-body">
                    <?php if($tecnicaEspecialidad->tecnicos && $tecnicaEspecialidad->tecnicos->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Técnico</th>
                                    <th>Especialidad</th>
                                    <th>Teléfono</th>
                                    <th>Órdenes</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $tecnicaEspecialidad->tecnicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tecnico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo e(route('tecnicos.show', $tecnico)); ?>" class="text-decoration-none">
                                            <?php echo e($tecnico->nombre); ?>

                                        </a>
                                    </td>
                                    <td>
                                        <?php if($tecnico->pivot && $tecnico->pivot->activo): ?>
                                        <span class="badge bg-success">Activa</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactiva</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($tecnico->telefono ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($tecnico->ordenes_reparacion_count ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($tecnico->activo ? 'bg-success' : 'bg-secondary'); ?>">
                                            <?php echo e($tecnico->activo_label); ?>

                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox d-block fs-1 mb-2"></i>
                        No hay técnicos asignados a esta especialidad
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tecnica-especialidades/show.blade.php ENDPATH**/ ?>