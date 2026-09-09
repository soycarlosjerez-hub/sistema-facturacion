<?php $__env->startSection('title', 'Ver Configuración de Garantía'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($garantiasConfig->nombre); ?></h4>
                    <div class="ui-header-meta">Detalles de la configuración de garantía</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('garantias-config.edit')): ?>
                <a href="<?php echo e(route('garantias-config.edit', $garantiasConfig)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('garantias-config.index')); ?>" class="ui-btn ui-btn-sm rounded-pill" style="background:rgba(255,255,255,.15);">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información de la Garantía</h6>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre</small>
                        <strong><?php echo e($garantiasConfig->nombre); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Producto</small>
                        <?php if($garantiasConfig->tipo_producto): ?>
                        <span class="badge bg-info"><?php echo e($garantiasConfig->tipo_producto); ?></span>
                        <?php else: ?>
                        <span class="text-muted">General (todos los productos)</span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Días de Garantía</small>
                        <span class="badge bg-success fs-6"><?php echo e($garantiasConfig->dias_garantia); ?> días</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo de Garantía</small>
                        <span class="badge <?php echo e($garantiasConfig->tipo_garantia == 'fabrica' ? 'bg-primary' : 'bg-warning text-dark'); ?> fs-6">
                            <?php echo e($garantiasConfig->tipo_garantia_label); ?>

                        </span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Orden de Visualización</small>
                        <strong><?php echo e($garantiasConfig->orden); ?></strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge <?php echo e($garantiasConfig->activo ? 'bg-success' : 'bg-secondary'); ?> fs-6">
                            <?php echo e($garantiasConfig->activo_label); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Cobertura</h6>
                    <?php if($garantiasConfig->cobertura): ?>
                    <p class="text-muted"><?php echo e($garantiasConfig->cobertura); ?></p>
                    <?php else: ?>
                    <p class="text-muted">Sin descripción de cobertura</p>
                    <?php endif; ?>

                    <h6 class="fw-bold mb-3 mt-4">Terminos por Defecto</h6>
                    <?php if($garantiasConfig->terminos_por_defecto): ?>
                    <p class="text-muted" style="white-space:pre-line;"><?php echo e($garantiasConfig->terminos_por_defecto); ?></p>
                    <?php else: ?>
                    <p class="text-muted">Sin terminos por defecto</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Información Adicional</h6>
                    <div class="mb-2">
                        <small class="text-muted d-block">Creada el</small>
                        <strong><?php echo e($garantiasConfig->created_at->format('d/m/Y H:i')); ?></strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">Última actualización</small>
                        <strong><?php echo e($garantiasConfig->updated_at->format('d/m/Y H:i')); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias-config/show.blade.php ENDPATH**/ ?>