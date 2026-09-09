<?php $__env->startSection('title', 'Certificados Digitales'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .card { background: rgba(15,23,42,.8); }
body.dark-mode .dropdown-menu { background: #1e293b; border-color: #334155; }
body.dark-mode .dropdown-item { color: #94a3b8; }
body.dark-mode .dropdown-item:hover { background: #334155; color: #f1f5f9; }
body.dark-mode .bg-light { background: rgba(30,41,59,.6) !important; }
body.dark-mode .alert-success { background: rgba(16,185,129,.15); color: #6ee7b7; }
body.dark-mode .alert-warning { background: rgba(245,158,11,.15); color: #fcd34d; }
body.dark-mode .alert-danger { background: rgba(239,68,68,.15); color: #fca5a5; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s;background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="<?php echo e(route('ecf.index')); ?>" class="text-decoration-none text-white-50">e-CF</a></li>
                            <li class="breadcrumb-item active text-white">Certificados Digitales</li>
                        </ol>
                    </nav>
                    <h4 class="ui-header-title">Certificados Digitales</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-key me-1"></i>
                        <span>Certificados para firma de e-CF (.p12 / .pfx) emitidos por entidad autorizada</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions d-flex gap-2">
                <a href="<?php echo e(route('ecf.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-receipt me-1"></i> Ver Documentos
                </a>
                <a href="<?php echo e(route('certificados-digitales.create')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Certificado
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat p-3" style="--delay:.1s">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.4rem;">
                        <i class="bi bi-key"></i>
                    </div>
                    <div>
                        <div class="ui-stat-label">Total</div>
                        <div class="ui-stat-value"><?php echo e($stats['total']); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat p-3" style="--delay:.15s">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.4rem;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="ui-stat-label">Vigentes</div>
                        <div class="ui-stat-value"><?php echo e($stats['vigentes']); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat p-3" style="--delay:.2s">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.4rem;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="ui-stat-label">Por Vencer (≤30d)</div>
                        <div class="ui-stat-value"><?php echo e($stats['por_vencer']); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat p-3" style="--delay:.25s">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:52px; height:52px; font-size:1.4rem;">
                        <i class="bi bi-x-circle"></i>
                    </div>
                    <div>
                        <div class="ui-stat-label">Vencidos</div>
                        <div class="ui-stat-value"><?php echo e($stats['vencidos']); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php $__empty_0 = true; $__currentLoopData = $certificados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cert): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
        <div class="col-xl-4 col-md-6">
            <div class="ui-card h-100 overflow-hidden" style="--delay:.3s">
                <div class="ui-card-accent"></div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-key-fill text-primary fs-4"></i>
                                <h5 class="fw-bold mb-0"><?php echo e($cert->nombre); ?></h5>
                            </div>
                            <small class="text-muted"><?php echo e($cert->emisor_cert ?? 'Emisor no especificado'); ?></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle btn-sm" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3">
                                <li><a class="dropdown-item" href="<?php echo e(route('certificados-digitales.edit', $cert)); ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                <li>
                                    <form action="<?php echo e(route('certificados-digitales.toggle', $cert)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button class="dropdown-item <?php echo e($cert->activo ? 'text-danger' : 'text-success'); ?>">
                                            <i class="bi <?php echo e($cert->activo ? 'bi-slash-circle' : 'bi-check-circle'); ?> me-2"></i>
                                            <?php echo e($cert->activo ? 'Desactivar' : 'Activar'); ?>

                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="<?php echo e(route('certificados-digitales.destroy', $cert)); ?>" method="POST">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="dropdown-item text-danger" onclick="event.preventDefault();UI.confirm.delete('<?php echo e(route('certificados-digitales.destroy', $cert)); ?>', '<?php echo e(addslashes($cert->nombre)); ?>')"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php if(!$cert->vigente()): ?>
                        <div class="alert alert-danger border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-x-octagon me-1"></i>Vencido el <?php echo e($cert->fecha_vencimiento->format('d/m/Y')); ?>

                        </div>
                    <?php elseif($cert->diasParaVencer() <= 30): ?>
                        <div class="alert alert-warning border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-clock-history me-1"></i>Vence en <?php echo e($cert->diasParaVencer()); ?> días
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success border-0 rounded-3 py-2 small mb-3">
                            <i class="bi bi-check-circle me-1"></i>Vigente hasta <?php echo e($cert->fecha_vencimiento->format('d/m/Y')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="row g-2 small mb-2">
                        <div class="col-6">
                            <span class="text-muted d-block">RNC Emisor</span>
                            <span class="fw-bold"><?php echo e($cert->rnc_emisor); ?></span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">RNC Titular</span>
                            <span class="fw-bold"><?php echo e($cert->rnc_titular); ?></span>
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Documentos firmados:</span>
                            <span class="fw-bold"><?php echo e($cert->documentos()->count()); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Serial:</span>
                            <span class="fw-bold text-truncate ms-2" style="max-width: 60%;"><?php echo e($cert->serial_number ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-key display-1 text-muted opacity-25"></i>
            <p class="text-muted mt-3">No hay certificados digitales registrados.</p>
            <a href="<?php echo e(route('certificados-digitales.create')); ?>" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>Registrar Primer Certificado
            </a>
            <p class="small text-muted mt-2">
                <i class="bi bi-info-circle me-1"></i>Si no tiene un certificado, el sistema usará firma simulada (modo sandbox).
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/certificados-digitales/index.blade.php ENDPATH**/ ?>