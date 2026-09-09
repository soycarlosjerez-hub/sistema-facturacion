<?php $__env->startSection('title', 'SGC - Dashboard'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #6366f1; --accent-rgb: 99,102,241; --accent-hover: #4f46e5; }
.sgc-stat-number { font-size: 2rem; font-weight: 800; line-height: 1; }
.sgc-stat-label { font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; opacity: .8; margin-top: .25rem; }
.sgc-stat-icon {
    width: 48px; height: 48px;
    border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.25rem;
}
.sgc-table {
    --bs-table-bg: transparent;
    width: 100%;
}
.sgc-table thead th {
    background: rgba(241,245,249,.8);
    color: #64748b;
    font-size: .7rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    padding: .75rem 1rem;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.sgc-table tbody td {
    padding: .75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: .85rem;
}
.sgc-table tbody tr:last-child td { border-bottom: none; }
.sgc-table tbody tr { transition: background .15s; }
.sgc-table tbody tr:hover { background: rgba(99,102,241,.03); }
.badge-status { font-size: .7rem; font-weight: 600; padding: .3rem .6rem; border-radius: .5rem; }
.badge-borrador { background: #f1f5f9; color: #64748b; }
.badge-revision { background: #fef3c7; color: #d97706; }
.badge-aprobado { background: #dbeafe; color: #2563eb; }
.badge-vigente { background: #dcfce7; color: #16a34a; }
.badge-obsoleto { background: #fee2e2; color: #dc2626; }
.badge-archivado { background: #f3e8ff; color: #7c3aed; }
.badge-pendiente { background: #fef3c7; color: #d97706; }
.badge-verificado { background: #dcfce7; color: #16a34a; }
.badge-por_cargar { background: #e0e7ff; color: #4338ca; }
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
                    <i class="bi bi-file-earmark-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Sistema de Gestión de Calidad</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-bar-chart-line me-1"></i> Resumen y monitoreo de documentos SGC
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:0s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number text-white"><?php echo e($stats['total']); ?></div>
                            <div class="sgc-stat-label text-white-50">Total Documentos</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(99,102,241,.2);color:#a5b4fc;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.05s">
                <div class="ui-card-accent" style="--accent:#22c55e;--accent-hover:#16a34a;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number text-success"><?php echo e($stats['vigentes']); ?></div>
                            <div class="sgc-stat-label text-muted">Vigentes</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(34,197,94,.15);color:#22c55e;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number" style="color:#f59e0b;"><?php echo e($stats['pendientes_revision']); ?></div>
                            <div class="sgc-stat-label text-muted">Pendientes Revisión</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(245,158,11,.15);color:#f59e0b;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.15s">
                <div class="ui-card-accent" style="--accent:#ef4444;--accent-hover:#dc2626;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number" style="color:#ef4444;"><?php echo e($stats['proximo_revision_30']); ?></div>
                            <div class="sgc-stat-label text-muted">Próx. 30 Días</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(239,68,68,.15);color:#ef4444;">
                            <i class="bi bi-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number" style="color:#8b5cf6;"><?php echo e($stats['documentos_proveedor_vigentes']); ?></div>
                            <div class="sgc-stat-label text-muted">Prov. Vigentes</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(139,92,246,.15);color:#8b5cf6;">
                            <i class="bi bi-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="ui-card" style="--delay:.25s">
                <div class="ui-card-accent" style="--accent:#ec4899;--accent-hover:#db2777;"></div>
                <div class="ui-card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="sgc-stat-number" style="color:#ec4899;"><?php echo e($stats['documentos_proveedor_pendientes']); ?></div>
                            <div class="sgc-stat-label text-muted">Prov. Pendientes</div>
                        </div>
                        <div class="sgc-stat-icon" style="background:rgba(236,72,153,.15);color:#ec4899;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="ui-card" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <span class="ui-badge ui-badge-primary rounded-pill px-3 py-2">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Acciones rápidas
                            </span>
                        </div>
                        <div class="col d-flex gap-2 flex-wrap">
                            <a href="<?php echo e(route('sgc.documentos.index')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
                                <i class="bi bi-list-ul"></i> Ver Documentos
                            </a>
                            <a href="<?php echo e(route('sgc.documentos.create')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                                <i class="bi bi-plus-lg"></i> Nuevo Documento
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.35s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Distribución por Categoría</h6>
                    <?php $__empty_0 = true; $__currentLoopData = $stats['por_categoria']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catKey => $catCount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted" style="font-size:.85rem;"><?php echo e($stats['categorias'][$catKey] ?? $catKey); ?></span>
                            <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:.75rem;"><?php echo e($catCount); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <p class="text-muted small mb-0">Sin documentos aún.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.4s">
                <div class="ui-card-accent" style="--accent:#8b5cf6;--accent-hover:#7c3aed;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i>Distribución por Estado</h6>
                    <?php $__empty_0 = true; $__currentLoopData = $stats['estados']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estKey => $estLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <?php $count = \App\Models\DocumentoSgc::where('estado', $estKey)->count(); ?>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted" style="font-size:.85rem;"><?php echo e($estLabel); ?></span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size:.75rem;"><?php echo e($count); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.45s">
                <div class="ui-card-accent" style="--accent:#f59e0b;--accent-hover:#d97706;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-calendar-event me-2"></i>Próximos a Revisión (30 días)</h6>
                    <?php if($proximoRevision->count()): ?>
                        <table class="sgc-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $proximoRevision; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><code><?php echo e($doc->codigo); ?></code></td>
                                    <td><?php echo e(Str::limit($doc->titulo, 30)); ?></td>
                                    <td><?php echo e($doc->fecha_revision ? $doc->fecha_revision->format('d/m/Y') : '-'); ?></td>
                                    <td><span class="badge-status badge-<?php echo e($doc->estado); ?>"><?php echo e($stats['estados'][$doc->estado] ?? $doc->estado); ?></span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <a href="<?php echo e(route('sgc.documentos.index')); ?>" class="text-decoration-none small" style="color:#6366f1;">
                                Ver todos <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay documentos próximos a revisión.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.5s">
                <div class="ui-card-accent" style="--accent:#ef4444;--accent-hover:#dc2626;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-exclamation-circle me-2"></i>Pendientes de Revisión</h6>
                    <?php if($pendientesRevision->count()): ?>
                        <table class="sgc-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $pendientesRevision; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><code><?php echo e($doc->codigo); ?></code></td>
                                    <td><?php echo e(Str::limit($doc->titulo, 30)); ?></td>
                                    <td><?php echo e($doc->fecha_revision ? $doc->fecha_revision->format('d/m/Y') : '-'); ?></td>
                                    <td><span class="badge-status badge-<?php echo e($doc->estado); ?>"><?php echo e($stats['estados'][$doc->estado] ?? $doc->estado); ?></span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                        <div class="mt-2">
                            <a href="<?php echo e(route('sgc.documentos.index')); ?>" class="text-decoration-none small" style="color:#6366f1;">
                                Ver todos <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay documentos pendientes de revisión.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.55s">
                <div class="ui-card-accent" style="--accent:#6366f1;--accent-hover:#4f46e5;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Documentos Recientes</h6>
                    <?php if($documentos->count()): ?>
                        <table class="sgc-table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Creado</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><code><?php echo e($doc->codigo); ?></code></td>
                                    <td><?php echo e(Str::limit($doc->titulo, 30)); ?></td>
                                    <td><?php echo e($doc->created_at ? $doc->created_at->format('d/m/Y') : '-'); ?></td>
                                    <td><span class="badge-status badge-<?php echo e($doc->estado); ?>"><?php echo e($stats['estados'][$doc->estado] ?? $doc->estado); ?></span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No hay documentos registrados.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card" style="--delay:.6s">
                <div class="ui-card-accent" style="--accent:#ec4899;--accent-hover:#db2777;"></div>
                <div class="ui-card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i>Documentos de Proveedores</h6>
                    <?php if($documentosProvPendientes->count()): ?>
                        <table class="sgc-table">
                            <thead>
                                <tr>
                                    <th>Proveedor</th>
                                    <th>Documento SGC</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $documentosProvPendientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($dp->proveedor ? $dp->proveedor->nombre : 'N/A'); ?></td>
                                    <td><?php echo e($dp->documentoSgc ? $dp->documentoSgc->codigo : 'N/A'); ?></td>
                                    <td><span class="badge-status badge-<?php echo e($dp->estado); ?>"><?php echo e($dp->estado); ?></span></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted small mb-0">Todos los documentos de proveedores están al día.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/dashboard.blade.php ENDPATH**/ ?>