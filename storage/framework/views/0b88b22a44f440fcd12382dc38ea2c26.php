<?php $__env->startSection('title', 'Objetivo de Calidad'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-en_curso { background: #dbeafe; color: #2563eb; }
    .badge-cumplido { background: #dcfce7; color: #16a34a; }
    .badge-no_cumplido { background: #fee2e2; color: #dc2626; }
    .badge-atrasado { background: #fef3c7; color: #d97706; }
    .medicion-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: 1rem; }
    .medicion-item:last-child { border-left-color: #a5b4fc; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($objetivo->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.objetivos.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        <?php echo e($objetivo->titulo); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-<?php echo e($objetivo->estado); ?> fs-6"><?php echo e($objetivo->estado_label); ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Detalles del Objetivo</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Código</div>
                            <div class="detail-value"><code><?php echo e($objetivo->codigo); ?></code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Indicador</div>
                            <div class="detail-value"><?php echo e($objetivo->indicador ?? '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($objetivo->estado); ?>"><?php echo e($objetivo->estado_label); ?></span></div>
                        </div>
                        <?php if($objetivo->descripcion): ?>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value"><?php echo e($objetivo->descripcion); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <div class="detail-label">Meta</div>
                            <div class="detail-value fw-bold"><?php echo e($objetivo->meta); ?> <?php echo e($objetivo->unidad); ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Valor Actual</div>
                            <div class="detail-value fw-bold"><?php echo e($objetivo->valor_actual ?? '0'); ?> <?php echo e($objetivo->unidad); ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Cumplimiento</div>
                            <div class="detail-value">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height:8px;">
                                        <div class="progress-bar <?php echo e($objetivo->cumplimiento_bar); ?>" style="width:<?php echo e(min($objetivo->cumplimiento ?? 0, 100)); ?>%"></div>
                                    </div>
                                    <strong><?php echo e($objetivo->cumplimiento ?? 0); ?>%</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="detail-label">Responsable</div>
                            <div class="detail-value"><?php echo e($objetivo->responsable ? $objetivo->responsable->name : 'Sin asignar'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Periodo</div>
                            <div class="detail-value"><?php echo e($objetivo->periodo_label); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2"></i>Historial de Mediciones (<?php echo e($objetivo->mediciones->count()); ?>)</h6>
                    <?php if($objetivo->mediciones->count()): ?>
                        <?php $__currentLoopData = $objetivo->mediciones->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="medicion-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold small"><?php echo e($med->valor); ?> <?php echo e($objetivo->unidad); ?></span>
                                    <span class="badge bg-primary bg-opacity-10 text-primary ms-1" style="font-size:.65rem;"><?php echo e($med->cumplimiento); ?>%</span>
                                </div>
                                <small class="text-muted"><?php echo e($med->created_at ? $med->created_at->format('d/m/Y H:i') : ''); ?></small>
                            </div>
                            <?php if($med->observaciones): ?>
                            <div class="text-muted small mt-1"><?php echo e($med->observaciones); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay mediciones registradas.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card mb-3" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i>Acciones</h6>
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('sgc.objetivos.edit', $objetivo)); ?>" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value"><?php echo e($objetivo->creador ? $objetivo->creador->name : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value"><?php echo e($objetivo->created_at ? $objetivo->created_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value"><?php echo e($objetivo->updated_at ? $objetivo->updated_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/objetivos/show.blade.php ENDPATH**/ ?>