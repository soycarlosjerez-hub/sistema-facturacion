<?php $__env->startSection('title', 'Plantilla de Gasto'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-richtext"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($plantilla->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-receipt me-1"></i>
                        Plantilla de gasto recurrente
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('plantilla-gastos.edit', $plantilla)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(139,92,246,.2);border-color:rgba(139,92,246,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?php echo e(route('plantilla-gastos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2" style="color:#8b5cf6;"></i> Información de la Plantilla</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value fw-semibold"><?php echo e($plantilla->nombre); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                <?php if($plantilla->activo ?? true): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactiva</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value"><?php echo e($plantilla->descripcion ?? 'Sin descripción'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Categoría</div>
                            <div class="detail-value"><?php echo e($plantilla->categoria ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Método de Pago</div>
                            <div class="detail-value"><?php echo e($plantilla->metodo_pago ? ucfirst($plantilla->metodo_pago) : '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">N° Comprobante</div>
                            <div class="detail-value"><?php echo e($plantilla->comprobante ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Notas</div>
                            <div class="detail-value"><?php echo e($plantilla->notas ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Creación</div>
                            <div class="detail-value"><?php echo e($plantilla->created_at->format('d/m/Y h:i A')); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(139,92,246,.1);">
                        <i class="bi bi-file-earmark-richtext fs-2" style="color:#8b5cf6;"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($plantilla->nombre); ?></h5>
                    <small class="text-muted">Plantilla de gasto recurrente</small>
                    <hr class="my-3">
                    <div class="text-start">
                        <small class="text-muted d-block">Creado: <span class="fw-semibold"><?php echo e($plantilla->created_at->format('d/m/Y')); ?></span></small>
                        <small class="text-muted d-block">Actualizado: <span class="fw-semibold"><?php echo e($plantilla->updated_at->format('d/m/Y')); ?></span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/plantilla-gastos/show.blade.php ENDPATH**/ ?>