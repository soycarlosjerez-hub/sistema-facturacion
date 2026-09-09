<?php $__env->startSection('title', 'Comprobantes Fiscales Electrónicos (e-CF)'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .ui-card { background: rgba(15,23,42,.8); }
body.dark-mode .ui-table { color: #cbd5e1; }
body.dark-mode .ui-table thead th { background: rgba(30,41,59,.6); color: #94a3b8; border-color: #334155; }
body.dark-mode .ui-table tbody td { border-color: #1e293b; }
body.dark-mode .ui-card .ui-input:focus,
body.dark-mode .ui-card .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .ui-card .ui-btn-solid { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .ui-card .ui-btn-solid:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .ui-card .form-check-input { background-color: #334155; border-color: #475569; }
body.dark-mode .form-check-input:checked { background-color: #f59e0b; border-color: #f59e0b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header d-flex justify-content-between align-items-center mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Comprobantes Fiscales Electrónicos</h4>
                    <div class="ui-header-meta">
                        <span>Gestión de e-CF según normas DGII - República Dominicana</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('secuencias-ecf.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-hash me-1"></i> Secuencias
                </a>
                <a href="<?php echo e(route('certificados-digitales.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-key me-1"></i> Certificados
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; background:rgba(59,130,246,.1); color:#3b82f6;">
                            <i class="bi bi-file-pdf"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Total Emitidos</div>
                            <div class="ui-stat-value"><?php echo e($stats['total']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; background:rgba(34,197,94,.1); color:#16a34a;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Aprobados</div>
                            <div class="ui-stat-value"><?php echo e($stats['aprobados']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; background:rgba(245,158,11,.1); color:#d97706;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Pendientes</div>
                            <div class="ui-stat-value"><?php echo e($stats['pendientes']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.3s">
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:52px; height:52px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; background:rgba(239,68,68,.1); color:#dc2626;">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div>
                            <div class="ui-stat-label">Rechazados</div>
                            <div class="ui-stat-value"><?php echo e($stats['rechazados']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-3" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="ui-label small fw-bold mb-1">eNCF</label>
                    <input type="text" name="encf" class="ui-input ui-input-sm rounded-3" value="<?php echo e(request('encf')); ?>" placeholder="E310000000001">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-bold mb-1">Tipo</label>
                    <select name="tipo_ecf" class="ui-select ui-select-sm rounded-3">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $nombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('tipo_ecf') === $key ? 'selected' : ''); ?>><?php echo e($key); ?> - <?php echo e($nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-bold mb-1">Estado</label>
                    <select name="estado" class="ui-select ui-select-sm rounded-3">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $estados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(request('estado') === $key ? 'selected' : ''); ?>><?php echo e($info['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-bold mb-1">Desde</label>
                    <input type="date" name="desde" class="ui-input ui-input-sm rounded-3" value="<?php echo e(request('desde')); ?>">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small fw-bold mb-1">Hasta</label>
                    <input type="date" name="hasta" class="ui-input ui-input-sm rounded-3" value="<?php echo e(request('hasta')); ?>">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="ui-btn ui-btn-solid ui-btn-sm rounded-pill flex-grow-1">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="<?php echo e(route('ecf.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="ui-table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">eNCF</th>
                        <th>Tipo</th>
                        <th>Cliente</th>
                        <th class="text-end">Monto</th>
                        <th class="text-center">Estado</th>
                        <th>Emisión</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_0 = true; $__currentLoopData = $ecfs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ecf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                    <?php $estadoInfo = $ecf->estado_info; ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-primary" style="letter-spacing:1px;"><?php echo e($ecf->encf); ?></span>
                            <br><small class="text-muted">Venta #<?php echo e(str_pad($ecf->venta_id, 5, '0', STR_PAD_LEFT)); ?></small>
                        </td>
                        <td>
                            <span class="ui-badge ui-badge-neutral"><?php echo e($ecf->tipo_ecf); ?></span>
                            <br><small class="text-muted"><?php echo e($ecf->tipo_nombre); ?></small>
                        </td>
                        <td>
                            <span class="fw-bold small"><?php echo e($ecf->venta?->cliente?->nombre ?? 'N/A'); ?></span>
                            <br><small class="text-muted"><?php echo e($ecf->venta?->cliente?->rnc_cedula ?: 'Sin RNC'); ?></small>
                        </td>
                        <td class="text-end fw-bold">RD$ <?php echo e(number_format($ecf->monto_total, 2)); ?></td>
                        <td class="text-center">
                            <span class="ui-badge ui-badge-<?php echo e($estadoInfo['color']); ?> rounded-pill px-3">
                                <i class="bi <?php echo e($estadoInfo['icon']); ?> me-1"></i><?php echo e($estadoInfo['label']); ?>

                            </span>
                        </td>
                        <td class="small">
                            <?php echo e($ecf->fecha_emision->format('d/m/Y')); ?>

                            <br><small class="text-muted"><?php echo e($ecf->fecha_emision->format('h:i A')); ?></small>
                        </td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('ecf.show', $ecf)); ?>" class="ui-action ui-action-view" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if($ecf->xml_content): ?>
                            <a href="<?php echo e(route('ecf.xml', $ecf)); ?>" target="_blank" class="ui-action ui-action-view" title="Ver XML" style="margin-left:4px;">
                                <i class="bi bi-filetype-xml"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-shield-check display-3 text-muted opacity-25"></i>
                            <p class="text-muted mt-3 mb-0">No hay comprobantes electrónicos emitidos.</p>
                            <small class="text-muted">Cree secuencias e-CF y emita comprobantes desde el POS.</small>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($ecfs->hasPages()): ?>
        <div class="card-footer bg-transparent border-0 py-3">
            <?php echo e($ecfs->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/ecf/index.blade.php ENDPATH**/ ?>