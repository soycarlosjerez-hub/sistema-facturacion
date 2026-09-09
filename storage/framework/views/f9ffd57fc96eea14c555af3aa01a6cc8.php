<?php $__env->startSection('title', 'Certificado Digital'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s;background:linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($certificado->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-shield-lock me-1"></i>
                        Certificado digital para firma de e-CF
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('certificados-digitales.edit', $certificado)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill" style="background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.35);">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <a href="<?php echo e(route('certificados-digitales.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-key me-2" style="color:#f59e0b;"></i> Información del Certificado</h5>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="detail-label">Nombre</div>
                            <div class="detail-value fw-semibold"><?php echo e($certificado->nombre); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Entidad Emisora</div>
                            <div class="detail-value"><?php echo e($certificado->emisor_cert ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">RNC del Emisor</div>
                            <div class="detail-value font-monospace"><?php echo e($certificado->rnc_emisor ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">RNC del Titular</div>
                            <div class="detail-value font-monospace"><?php echo e($certificado->rnc_titular ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Número de Serie</div>
                            <div class="detail-value font-monospace"><?php echo e($certificado->serial_number ?? '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value">
                                <?php if($certificado->activo): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill"><i class="bi bi-check-circle-fill me-1"></i>Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill"><i class="bi bi-x-circle-fill me-1"></i>Inactivo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Emisión</div>
                            <div class="detail-value"><?php echo e($certificado->fecha_emision ? $certificado->fecha_emision->format('d/m/Y') : '—'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Fecha de Vencimiento</div>
                            <div class="detail-value">
                                <?php $isExpired = $certificado->fecha_vencimiento && $certificado->fecha_vencimiento->isPast(); ?>
                                <span class="<?php echo e($isExpired ? 'text-danger fw-bold' : ''); ?>">
                                    <?php echo e($certificado->fecha_vencimiento ? $certificado->fecha_vencimiento->format('d/m/Y') : '—'); ?>

                                </span>
                                <?php if($isExpired): ?>
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill ms-1" style="font-size:.65rem;">Vencido</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="detail-label">Notas</div>
                            <div class="detail-value"><?php echo e($certificado->notas ?? '—'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="background:#f59e0b"></div>
                <div class="ui-card-body text-center">
                    <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:rgba(245,158,11,.1);">
                        <i class="bi bi-key fs-2" style="color:#f59e0b;"></i>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($certificado->nombre); ?></h5>
                    <small class="text-muted">Certificado para firma electrónica</small>
                    <hr class="my-3">
                    <div class="text-start">
                        <small class="text-muted d-block">Creado: <span class="fw-semibold"><?php echo e($certificado->created_at->format('d/m/Y')); ?></span></small>
                        <small class="text-muted d-block">Última actualización: <span class="fw-semibold"><?php echo e($certificado->updated_at->format('d/m/Y')); ?></span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/certificados-digitales/show.blade.php ENDPATH**/ ?>