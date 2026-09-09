<?php $__env->startSection('title', 'Mejora Continua'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-propuesta { background: #f1f5f9; color: #64748b; }
    .badge-evaluando { background: #dbeafe; color: #2563eb; }
    .badge-aprobada { background: #e0f2fe; color: #0284c7; }
    .badge-en_curso { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-verificada { background: #d1fae5; color: #059669; }
    .badge-cerrada { background: #f1f5f9; color: #475569; }
    .badge-baja { background: #dbeafe; color: #2563eb; }
    .badge-media { background: #fef3c7; color: #d97706; }
    .badge-alta { background: #fed7aa; color: #ea580c; }
    .badge-urgente { background: #fee2e2; color: #dc2626; }
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
                    <i class="bi bi-arrow-up-circle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($mejora->numero_label); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.mejora.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        <?php echo e($mejora->titulo); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-<?php echo e($mejora->fase); ?> fs-6"><?php echo e($mejora->fase_label); ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Mejora</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Número</div>
                            <div class="detail-value"><code><?php echo e($mejora->numero_label); ?></code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fase</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($mejora->fase); ?>"><?php echo e($mejora->fase_label); ?></span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Prioridad</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($mejora->prioridad); ?>"><?php echo e($mejora->prioridad_label); ?></span></div>
                        </div>
                        <div class="col-12">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value"><?php echo e($mejora->descripcion); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Origen</div>
                            <div class="detail-value"><?php echo e(ucfirst($mejora->origen ?? '-')); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Impacto</div>
                            <div class="detail-value"><?php echo e($mejora->impacto_label); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Responsable</div>
                            <div class="detail-value"><?php echo e($mejora->responsable_label); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Propuesta</div>
                            <div class="detail-value"><?php echo e($mejora->fecha_propuesta ? $mejora->fecha_propuesta->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Límite</div>
                            <div class="detail-value"><?php echo e($mejora->fecha_limite ? $mejora->fecha_limite->format('d/m/Y') : 'Sin límite'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Completar</div>
                            <div class="detail-value"><?php echo e($mejora->fecha_completar ? $mejora->fecha_completar->format('d/m/Y') : '-'); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-currency-dollar me-2"></i>Beneficios</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Beneficios Esperados</div>
                            <div class="detail-value"><?php echo e($mejora->beneficios_esperados ?? '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Beneficios Logrados</div>
                            <div class="detail-value"><?php echo e($mejora->beneficios_logrados ?? '—'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Ahorro Estimado</div>
                            <div class="detail-value"><?php echo e($mejora->ahorro_estimado_label); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Costo Estimado</div>
                            <div class="detail-value"><?php echo e($mejora->costo_estimado_label); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb me-2"></i>Propuestas (<?php echo e($mejora->propuestas->count()); ?>)</h6>
                    <?php if($mejora->propuestas->count()): ?>
                        <?php $__currentLoopData = $mejora->propuestas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border-start border-3 border-primary ps-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong class="small"><?php echo e($prop->titulo); ?></strong>
                                <span class="badge-status badge-<?php echo e($prop->estado); ?>"><?php echo e($prop->estado_label); ?></span>
                            </div>
                            <div class="text-muted small"><?php echo e($prop->autor_label); ?> — <?php echo e($prop->fecha_label); ?></div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay propuestas registradas.</p>
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
                        <?php if(in_array($mejora->fase, ['propuesta', 'evaluando'])): ?>
                        <form action="<?php echo e(route('sgc.mejora.completar', $mejora)); ?>" method="POST" class="d-grid">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if(in_array($mejora->fase, ['completada', 'verificada'])): ?>
                        <form action="<?php echo e(route('sgc.mejora.cerrar', $mejora)); ?>" method="POST" class="d-grid" onsubmit="return confirm('¿Cerrar esta mejora?')">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-archive me-1"></i> Cerrar
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value"><?php echo e($mejora->creador ? $mejora->creador->name : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value"><?php echo e($mejora->created_at ? $mejora->created_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value"><?php echo e($mejora->updated_at ? $mejora->updated_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/mejora_continua/show.blade.php ENDPATH**/ ?>