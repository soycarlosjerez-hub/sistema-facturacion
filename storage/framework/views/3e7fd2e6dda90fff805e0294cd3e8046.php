<?php $__env->startSection('title', 'Auditoría Interna'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-cancelada { background: #f1f5f9; color: #64748b; }
    .checklist-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: .75rem; }
    .checklist-item:last-child { border-left-color: #a5b4fc; }
    .hallazgo-card { border: 1px solid #e2e8f0; border-radius: .75rem; padding: 1rem; margin-bottom: .75rem; }
    .hallazgo-card:last-child { margin-bottom: 0; }
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
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($auditoria->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.auditorias.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Auditoría — <?php echo e($auditoria->area_auditar); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-<?php echo e($auditoria->estado); ?> fs-6"><?php echo e($auditoria->estado_label); ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Auditoría</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Código</div>
                            <div class="detail-value"><code><?php echo e($auditoria->codigo); ?></code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Área a Auditar</div>
                            <div class="detail-value"><?php echo e($auditoria->area_auditar); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($auditoria->estado); ?>"><?php echo e($auditoria->estado_label); ?></span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Programada</div>
                            <div class="detail-value"><?php echo e($auditoria->fecha_programada ? $auditoria->fecha_programada->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Inicio Real</div>
                            <div class="detail-value"><?php echo e($auditoria->fecha_real_inicio ? $auditoria->fecha_real_inicio->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Fin Real</div>
                            <div class="detail-value"><?php echo e($auditoria->fecha_real_fin ? $auditoria->fecha_real_fin->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Responsable Auditor</div>
                            <div class="detail-value"><?php echo e($auditoria->responsable_auditor_label); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Programa</div>
                            <div class="detail-value"><?php echo e($auditoria->programa_label); ?></div>
                        </div>
                        <?php if($auditoria->alcance): ?>
                        <div class="col-12">
                            <div class="detail-label">Alcance</div>
                            <div class="detail-value"><?php echo e($auditoria->alcance); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if($auditoria->criterios): ?>
                        <div class="col-12">
                            <div class="detail-label">Criterios</div>
                            <div class="detail-value"><?php echo e($auditoria->criterios); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if($auditoria->cumplimiento_general): ?>
                        <div class="col-md-4">
                            <div class="detail-label">Cumplimiento General</div>
                            <div class="detail-value fw-bold"><?php echo e($auditoria->cumplimiento_general_label); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i>Checklist (<?php echo e($auditoria->checklistItems->count()); ?>)</h6>
                    <?php if($auditoria->checklistItems->count()): ?>
                        <?php $__currentLoopData = $auditoria->checklistItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="checklist-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-bold small"><?php echo e($item->descripcion ?? 'Item #' . $item->id); ?></span>
                                    <?php if(isset($item->cumplimiento)): ?>
                                    <span class="badge-status badge-<?php echo e($item->cumplimiento === 'conforme' ? 'completada' : 'programada'); ?> ms-1"><?php echo e($item->cumplimiento); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if(isset($item->observacion) && $item->observacion): ?>
                            <div class="text-muted small mt-1"><?php echo e($item->observacion); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay items en el checklist.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-circle me-2"></i>Hallazgos (<?php echo e($auditoria->hallazgos_count); ?>)</h6>
                    <?php if($auditoria->hallazgos->count()): ?>
                        <?php $__currentLoopData = $auditoria->hallazgos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hallazgo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="hallazgo-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge-status badge-<?php echo e($hallazgo->tipo ?? 'programada'); ?>"><?php echo e($hallazgo->tipo ?? 'Hallazgo'); ?></span>
                                <small class="text-muted"><?php echo e($hallazgo->created_at ? $hallazgo->created_at->format('d/m/Y') : ''); ?></small>
                            </div>
                            <p class="mb-1 small"><?php echo e($hallazgo->descripcion ?? 'Sin descripción'); ?></p>
                            <?php if(isset($hallazgo->area)): ?>
                            <small class="text-muted">Área: <?php echo e($hallazgo->area); ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay hallazgos registrados.</p>
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
                        <?php if($auditoria->estado === 'programada'): ?>
                        <form action="<?php echo e(route('sgc.auditorias.iniciar', $auditoria)); ?>" method="POST" class="d-grid">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-play me-1"></i> Iniciar Auditoría
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if($auditoria->estado === 'en_curso'): ?>
                        <form action="<?php echo e(route('sgc.auditorias.completar', $auditoria)); ?>" method="POST" class="d-grid" onsubmit="return confirm('¿Marcar auditoría como completada?')">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="<?php echo e(route('sgc.auditorias.informe', $auditoria)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> Ver Informe
                        </a>
                    </div>
                </div>
            </div>

            
            <div class="ui-card mb-3" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Resumen</h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="detail-label">Checklist</div>
                            <div class="detail-value fw-bold"><?php echo e($auditoria->checklistItems->count()); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Conformes</div>
                            <div class="detail-value fw-bold text-success"><?php echo e($auditoria->conformes_count); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Hallazgos</div>
                            <div class="detail-value fw-bold text-danger"><?php echo e($auditoria->hallazgos_count); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Observaciones</div>
                            <div class="detail-value fw-bold text-warning"><?php echo e($auditoria->observaciones_count); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value"><?php echo e($auditoria->creador ? $auditoria->creador->name : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value"><?php echo e($auditoria->created_at ? $auditoria->created_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value"><?php echo e($auditoria->updated_at ? $auditoria->updated_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/auditorias/show.blade.php ENDPATH**/ ?>