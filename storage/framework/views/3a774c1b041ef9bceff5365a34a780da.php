<?php $__env->startSection('title', 'Documento SGC'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.detail-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .5px; color: #64748b; font-weight: 600; margin-bottom: .15rem; }
.detail-value { font-size: .9rem; color: #1e293b; font-weight: 500; }
.badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
.badge-borrador { background: #f1f5f9; color: #64748b; }
.badge-revision { background: #fef3c7; color: #d97706; }
.badge-aprobado { background: #dbeafe; color: #2563eb; }
.badge-vigente { background: #dcfce7; color: #16a34a; }
.badge-obsoleto { background: #fee2e2; color: #dc2626; }
.badge-archivado { background: #f3e8ff; color: #7c3aed; }
.version-item { border-left: 3px solid #6366f1; padding-left: 1rem; margin-bottom: 1rem; }
.version-item:last-child { border-left-color: #a5b4fc; }
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
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h4 class="ui-header-title"><?php echo e($documento->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.documentos.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Detalle del documento SGC
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <span class="badge-status badge-<?php echo e($documento->estado); ?> fs-6"><?php echo e($documento->estado); ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">
        
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h5 class="fw-bold mb-3"><?php echo e($documento->titulo); ?></h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="detail-label">Descripción</div>
                            <div class="detail-value"><?php echo e($documento->descripcion ?? 'Sin descripción'); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Categoría</div>
                            <div class="detail-value"><?php echo e($documento->categoria); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Versión Actual</div>
                            <div class="detail-value">v<?php echo e($documento->version); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Nº Versiones</div>
                            <div class="detail-value"><?php echo e($documento->numero_versiones); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Estado</div>
                            <div class="detail-value"><span class="badge-status badge-<?php echo e($documento->estado); ?>"><?php echo e($documento->estado); ?></span></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Emisión</div>
                            <div class="detail-value"><?php echo e($documento->fecha_emision ? $documento->fecha_emision->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Revisión</div>
                            <div class="detail-value"><?php echo e($documento->fecha_revision ? $documento->fecha_revision->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Fecha Vencimiento</div>
                            <div class="detail-value"><?php echo e($documento->fecha_vencimiento ? $documento->fecha_vencimiento->format('d/m/Y') : '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Formato</div>
                            <div class="detail-value"><?php echo e($documento->formato ?? '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Próxima Revisión</div>
                            <div class="detail-value"><?php echo e($documento->proxima_revision ?? '-'); ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">¿Vigente?</div>
                            <div class="detail-value"><?php echo e($documento->esta_vigente ? '<i class="bi bi-check-circle text-success"></i> Sí' : '<i class="bi bi-x-circle text-danger"></i> No'); ?> </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-label">Proveedor</div>
                            <div class="detail-value"><?php echo e($documento->proveedor ? $documento->proveedor->nombre : '-'); ?></div>
                        </div>
                    </div>

                    
                    <?php if($documento->archivo_path): ?>
                    <div class="mt-3 pt-3 border-top">
                        <div class="detail-label">Archivo Adjunto</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <i class="bi bi-file-earmark-fill text-primary"></i>
                            <a href="<?php echo e(route('sgc.documentos.archivo.show', $documento)); ?>" class="text-decoration-none small" style="color:#6366f1;">
                                Descargar archivo
                            </a>
                            <?php if($documento->archivo_original_name): ?>
                            <span class="text-muted small ms-2">(<?php echo e($documento->archivo_original_name); ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-muted small mt-1">
                            <?php echo e($documento->archivo_size_bytes ? number_format($documento->archivo_size_bytes / 1024, 1) . ' KB' : ''); ?>

                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Historial de Versiones (<?php echo e($documento->numero_versiones); ?>)</h6>
                    <?php if($documento->versiones): ?>
                        <?php $__currentLoopData = $documento->versiones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $ver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="version-item">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.7rem;">v<?php echo e($ver['version']); ?></span>
                                    <span class="badge-status badge-<?php echo e($ver['estado'] ?? $documento->estado); ?> ms-1"><?php echo e($ver['estado'] ?? $documento->estado); ?></span>
                                    <?php if($idx === count($documento->versiones) - 1): ?>
                                    <span class="badge bg-success bg-opacity-10 text-success ms-1" style="font-size:.65rem;">ACTUAL</span>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted"><?php echo e(isset($ver['created_at']) ? \Carbon\Carbon::parse($ver['created_at'])->format('d/m/Y H:i') : ''); ?></small>
                            </div>
                            <?php if($ver['archivo_name'] ?? null): ?>
                            <div class="text-muted small mt-1">Archivo: <?php echo e($ver['archivo_name']); ?></div>
                            <?php endif; ?>
                            <?php if($ver['cambio'] ?? null && $idx > 0): ?>
                            <div class="text-muted small mt-1">Cambios: <?php echo e(json_encode($ver['cambio'])); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <?php if($documento->estado !== 'obsoleto' && $documento->estado !== 'archivado'): ?>
                        <a href="<?php echo e(route('sgc.documentos.edit', $documento)); ?>" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                        <?php endif; ?>
                        <?php if($documento->estado === 'borrador'): ?>
                        <form action="<?php echo e(route('sgc.documentos.aprobar', $documento)); ?>" method="POST" class="d-grid">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Aprobar
                            </button>
                        </form>
                        <form action="<?php echo e(route('sgc.documentos.rechazar', $documento)); ?>" method="POST" class="d-grid">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-x-lg me-1"></i> Rechazar
                            </button>
                        </form>
                        <?php endif; ?>
                        <?php if(in_array($documento->estado, ['borrador', 'revision', 'aprobado', 'vigente'])): ?>
                        <form action="<?php echo e(route('sgc.documentos.obsoleto', $documento)); ?>" method="POST" class="d-grid" onsubmit="return confirm('¿Marcar como obsoleto?')">
                            <?php echo csrf_field(); ?>
                            <button class="btn btn-sm btn-outline-warning rounded-pill">
                                <i class="bi bi-slash-lg me-1"></i> Marcar Obsoleto
                            </button>
                        </form>
                        <?php endif; ?>
                        <form action="<?php echo e(route('sgc.documentos.destroy', $documento)); ?>" method="POST" class="d-grid" onsubmit="return confirm('¿Eliminar documento?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger rounded-pill">
                                <i class="bi bi-trash me-1"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2"></i>Auditoría</h6>
                    <div class="mb-2">
                        <div class="detail-label">Creado Por</div>
                        <div class="detail-value"><?php echo e($documento->creador ? $documento->creador->name : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Creado</div>
                        <div class="detail-value"><?php echo e($documento->created_at ? $documento->created_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado Por</div>
                        <div class="detail-value"><?php echo e($documento->modificador ? $documento->modificador->name : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Modificado</div>
                        <div class="detail-value"><?php echo e($documento->updated_at ? $documento->updated_at->format('d/m/Y H:i') : '-'); ?></div>
                    </div>
                    <div class="mb-2">
                        <div class="detail-label">Aprobado Por</div>
                        <div class="detail-value"><?php echo e($documento->aprobador ? $documento->aprobador->name : '-'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/documentos/show.blade.php ENDPATH**/ ?>