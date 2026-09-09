<?php $__env->startSection('title', 'Ver Licencia de Software'); ?>

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
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Licencia: <?php echo e($licenciaSoftware->clave_licencia); ?></h4>
                    <div class="ui-header-meta">Detalles de la licencia de software</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('licencias-software.edit')): ?>
                <a href="<?php echo e(route('licencias-software.edit', $licenciaSoftware)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('licencias-software.index')); ?>" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información de la Licencia</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Clave de Licencia</small>
                        <code class="d-block p-2 bg-light rounded"><?php echo e($licenciaSoftware->clave_licencia); ?></code>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Producto</small>
                        <strong><?php echo e($licenciaSoftware->producto->nombre ?? '-'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Licencia</small>
                        <span class="badge bg-info"><?php echo e($licenciaSoftware->tipo_licencia ?? 'N/A'); ?></span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Plataforma</small>
                        <span class="badge bg-secondary"><?php echo e($licenciaSoftware->plataforma ?? 'N/A'); ?></span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Usuario Asignado</small>
                        <strong><?php echo e($licenciaSoftware->usuario_asignado ?? 'Sin asignar'); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <?php
                            $estado = 'Activa';
                            $badgeClass = 'success';
                            
                            if (!$licenciaSoftware->licencia_activa) {
                                $estado = 'Inactiva';
                                $badgeClass = 'secondary';
                            } elseif ($licenciaSoftware->fecha_vencimiento && $licenciaSoftware->fecha_vencimiento->lt(now())) {
                                $estado = 'Vencida';
                                $badgeClass = 'danger';
                            } elseif ($licenciaSoftware->fecha_vencimiento && $licenciaSoftware->fecha_vencimiento->lte(now()->addDays(30))) {
                                $estado = 'Por Vencer';
                                $badgeClass = 'warning';
                            }
                        ?>
                        <span class="badge bg-<?php echo e($badgeClass); ?> fs-6"><?php echo e($estado); ?></span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Fecha de Vencimiento</small>
                        <?php if($licenciaSoftware->fecha_vencimiento): ?>
                        <strong><?php echo e($licenciaSoftware->fecha_vencimiento->format('d/m/Y')); ?></strong>
                        <?php if($licenciaSoftware->dias_hasta_vencer !== null): ?>
                        <span class="ms-2 text-muted">(<?php echo e($licenciaSoftware->dias_hasta_vencer); ?> días)</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-muted">Sin vencimiento</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Notas</h6>
                    <?php if($licenciaSoftware->notas): ?>
                    <p class="text-muted"><?php echo e($licenciaSoftware->notas); ?></p>
                    <?php else: ?>
                    <p class="text-muted">No hay notas registradas</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información Adicional</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Creada el</small>
                        <strong><?php echo e($licenciaSoftware->created_at->format('d/m/Y H:i')); ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Última actualización</small>
                        <strong><?php echo e($licenciaSoftware->updated_at->format('d/m/Y H:i')); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/licencias-software/show.blade.php ENDPATH**/ ?>