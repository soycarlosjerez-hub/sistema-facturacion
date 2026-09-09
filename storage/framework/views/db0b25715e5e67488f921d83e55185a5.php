<?php $__env->startSection('title', 'Revisión por Dirección'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
    .detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
    .badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
    .badge-programada { background: #dbeafe; color: #2563eb; }
    .badge-en_ejecucion { background: #fef3c7; color: #d97706; }
    .badge-completada { background: #dcfce7; color: #16a34a; }
    .badge-programada_type { background: #dbeafe; color: #2563eb; }
    .badge-extraordinaria { background: #fee2e2; color: #dc2626; }
    .entrada-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: .75rem; }
    .salida-item { border-left: 3px solid #f59e0b; padding-left: 1rem; margin-bottom: .75rem; }
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
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Revisión #<?php echo e($revision->numero ?? '-'); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.revision-direccion.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        <?php echo e($revision->fecha ? $revision->fecha->format('d/m/Y') : ''); ?> — <?php echo e($revision->tipo_label); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-<?php echo e($revision->estado); ?> fs-6"><?php echo e($revision->estado_label); ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3">Información de la Revisión</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="detail-label">Nº</div>
                            <div class="detail-value"><code><?php echo e($revision->numero ? '#' . $revision->numero : '-'); ?></code></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha</div>
                            <div class="detail-value"><?php echo e($revision->fecha ? $revision->fecha->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Tipo</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($revision->tipo); ?>"><?php echo e($revision->tipo_label); ?></span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Duración</div>
                            <div class="detail-value"><?php echo e($revision->duracion_label); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($revision->estado); ?>"><?php echo e($revision->estado_label); ?></span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Creado Por</div>
                            <div class="detail-value"><?php echo e($revision->creador_label); ?></div>
                        </div>
                        <?php if($revision->resumen): ?>
                        <div class="col-12">
                            <div class="detail-label">Resumen</div>
                            <div class="detail-value"><?php echo e($revision->resumen); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if($revision->resumen_resoluciones): ?>
                        <div class="col-12">
                            <div class="detail-label">Resumen de Resoluciones</div>
                            <div class="detail-value"><?php echo e($revision->resumen_resoluciones); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i>Asistentes (<?php echo e($revision->asistentes_count); ?>)</h6>
                    <?php if($revision->asistentes->count()): ?>
                        <div class="table-responsive">
                        <table class="ui-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cargo</th>
                                    <th>Asistió</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $revision->asistentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asist): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($asist->nombre ?? $asist->usuario?->name ?? '-'); ?></td>
                                    <td><?php echo e($asist->cargo ?? '-'); ?></td>
                                    <td>
                                        <?php if($asist->asistio): ?>
                                            <i class="bi bi-check-circle-fill text-success"></i> Sí
                                        <?php else: ?>
                                            <i class="bi bi-x-circle-fill text-danger"></i> No
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay asistentes registrados.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#3b82f6;--accent-hover:#2563eb;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-down-left me-2"></i>Entradas (<?php echo e($revision->entradas->count()); ?>)</h6>
                    <?php if($revision->entradas->count()): ?>
                        <?php $__currentLoopData = $revision->entradas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entrada): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="entrada-item">
                            <div class="fw-bold small"><?php echo e($entrada->titulo ?? 'Entrada #' . $entrada->id); ?></div>
                            <?php if(isset($entrada->descripcion)): ?>
                            <div class="text-muted small"><?php echo e($entrada->descripcion); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay entradas registradas.</p>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-up-right me-2"></i>Salidas / Acuerdos (<?php echo e($revision->salidas_count); ?>)</h6>
                    <?php if($revision->salidas->count()): ?>
                        <?php $__currentLoopData = $revision->salidas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salida): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="salida-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-bold small"><?php echo e($salida->titulo ?? 'Salida #' . $salida->id); ?></div>
                                <?php if(isset($salida->estado)): ?>
                                <span class="badge-status badge-<?php echo e($salida->estado === 'pendiente' ? 'programada' : 'completada'); ?>"><?php echo e($salida->estado); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if(isset($salida->responsable)): ?>
                            <div class="text-muted small">Responsable: <?php echo e($salida->responsable); ?></div>
                            <?php endif; ?>
                            <?php if(isset($salida->fecha_limite)): ?>
                            <div class="text-muted small">Fecha límite: <?php echo e($salida->fecha_limite->format('d/m/Y')); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay salidas registradas.</p>
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
                        <?php if($revision->estado === 'programada'): ?>
                        <form action="<?php echo e(route('sgc.revision-direccion.completar', $revision)); ?>" method="POST" class="d-grid">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Completar
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="<?php echo e(route('sgc.revision-direccion.acta', $revision)); ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="bi bi-file-earmark-text me-1"></i> Ver Acta
                        </a>
                    </div>
                </div>
            </div>

            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i>Resumen</h6>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="detail-label">Asistentes</div>
                            <div class="detail-value fw-bold"><?php echo e($revision->asistentes_count); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Presentes</div>
                            <div class="detail-value fw-bold text-success"><?php echo e($revision->asistentes_presentes_count); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Entradas</div>
                            <div class="detail-value fw-bold text-primary"><?php echo e($revision->entradas->count()); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Salidas</div>
                            <div class="detail-value fw-bold text-warning"><?php echo e($revision->salidas_count); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/revision_direccion/show.blade.php ENDPATH**/ ?>