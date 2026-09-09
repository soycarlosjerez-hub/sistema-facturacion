<?php $__env->startSection('title', 'Todos los Errores'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .error-row { cursor: pointer; transition: background 0.15s; }
    .error-row:hover { background: #fef2f2 !important; }
    .trace-block {
        background: #1e293b; color: #e2e8f0; border-radius: 0.75rem;
        padding: 1rem; font-family: 'SF Mono', 'Fira Code', monospace;
        font-size: 0.75rem; max-height: 300px; overflow-y: auto;
        white-space: pre-wrap; word-break: break-all;
    }
    .context-key { color: #60a5fa; }
    .context-value { color: #fbbf24; }
    .resolve-btn { transition: all .2s ease; }
    .resolve-btn:hover { transform: scale(1.05); }
    .resolved-badge { cursor: default; }
    body.dark-mode .error-row:hover { background: rgba(239,68,68,.1) !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Todos los Errores</h4>
                    <small class="opacity-75"><i class="bi bi-globe me-1"></i>Todas las instancias &middot; Visi&oacute;n global</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="ui-stat-card h-100" style="--delay:.15s">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-list-check fs-4"></i>
                    </div>
                    <div>
                        <small class="stat-label d-block">Total</small>
                        <h3 class="stat-value mb-0"><?php echo e(number_format($stats['total'])); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat-card h-100" style="--delay:.2s">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-bug-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="stat-label d-block">Errores</small>
                        <h3 class="stat-value mb-0"><?php echo e(number_format($stats['errors'])); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat-card h-100" style="--delay:.25s">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="stat-label d-block">Warnings</small>
                        <h3 class="stat-value mb-0"><?php echo e(number_format($stats['warnings'])); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat-card h-100" style="--delay:.3s">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="bg-dark bg-opacity-10 text-dark rounded-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <i class="bi bi-x-octagon-fill fs-4"></i>
                    </div>
                    <div>
                        <small class="stat-label d-block">Cr&iacute;ticos</small>
                        <h3 class="stat-value mb-0"><?php echo e(number_format($stats['criticals'])); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.35s">
        <div class="ui-card-accent" style="background:#ef4444"></div>
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Instancia</label>
                    <select name="instance_id" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php $__currentLoopData = $instances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($inst->id); ?>" <?php echo e(request('instance_id') == $inst->id ? 'selected' : ''); ?>><?php echo e($inst->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Nivel</label>
                    <select name="level" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="error" <?php echo e(request('level') == 'error' ? 'selected' : ''); ?>>Error</option>
                        <option value="warning" <?php echo e(request('level') == 'warning' ? 'selected' : ''); ?>>Warning</option>
                        <option value="critical" <?php echo e(request('level') == 'critical' ? 'selected' : ''); ?>>Critical</option>
                        <option value="info" <?php echo e(request('level') == 'info' ? 'selected' : ''); ?>>Info</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Fuente</label>
                    <select name="source" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="exception" <?php echo e(request('source') == 'exception' ? 'selected' : ''); ?>>Excepci&oacute;n</option>
                        <option value="log" <?php echo e(request('source') == 'log' ? 'selected' : ''); ?>>Log</option>
                        <option value="ecf" <?php echo e(request('source') == 'ecf' ? 'selected' : ''); ?>>e-CF</option>
                        <option value="dgii" <?php echo e(request('source') == 'dgii' ? 'selected' : ''); ?>>DGII</option>
                        <option value="print" <?php echo e(request('source') == 'print' ? 'selected' : ''); ?>>Impresi&oacute;n</option>
                        <option value="email" <?php echo e(request('source') == 'email' ? 'selected' : ''); ?>>Email</option>
                        <option value="validation" <?php echo e(request('source') == 'validation' ? 'selected' : ''); ?>>Validaci&oacute;n</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Estado</label>
                    <select name="resolved" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="0" <?php echo e(request('resolved') === '0' ? 'selected' : ''); ?>>Pendientes</option>
                        <option value="1" <?php echo e(request('resolved') === '1' ? 'selected' : ''); ?>>Resueltos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold small text-muted">Buscar</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Mensaje..." value="<?php echo e(request('search')); ?>">
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-bold small text-muted">&nbsp;</label>
                    <button type="submit" class="ui-btn ui-btn-danger btn-sm rounded-pill w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card" style="--delay:.4s">
        <div class="ui-card-accent" style="background:#ef4444"></div>
        <div class="card-header bg-transparent border-0 p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-bug text-danger me-2"></i>Registro de Errores</h5>
            <small class="text-muted"><?php echo e($errorLogs->total()); ?> resultado(s)</small>
        </div>
        <div class="card-body p-0">
            <?php if($errorLogs->isNotEmpty()): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Instancia</th>
                            <th style="width:130px;">Fecha</th>
                            <th style="width:90px;">Nivel</th>
                            <th style="width:110px;">Fuente</th>
                            <th>Mensaje</th>
                            <th style="width:120px;">Usuario</th>
                            <th style="width:100px;">Estado</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $errorLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="error-row" data-bs-toggle="modal" data-bs-target="#errorModal<?php echo e($log->id); ?>">
                            <td>
                                <small class="fw-bold"><?php echo e($log->tenant?->nombre ?? '—'); ?></small>
                            </td>
                            <td><small class="text-muted"><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></small></td>
                            <td>
                                <span class="badge bg-<?php echo e($log->level_color); ?> bg-opacity-10 text-<?php echo e($log->level_color); ?> rounded-pill text-uppercase" style="font-size:.65rem;">
                                    <?php echo e($log->level); ?>

                                </span>
                            </td>
                            <td>
                                <i class="bi <?php echo e($log->source_icon); ?> me-1 text-muted"></i>
                                <small><?php echo e(ucfirst($log->source)); ?></small>
                            </td>
                            <td class="text-truncate" style="max-width:280px;"><?php echo e($log->title); ?></td>
                            <td><small><?php echo e($log->user?->name ?? '—'); ?></small></td>
                            <td>
                                <?php if($log->resolved): ?>
                                    <span class="badge bg-success rounded-pill resolved-badge">
                                        <i class="bi bi-check-circle me-1"></i>Resuelto
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill resolved-badge">
                                        <i class="bi bi-clock me-1"></i>Pendiente
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><i class="bi bi-chevron-right text-muted"></i></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if($errorLogs->hasPages()): ?>
            <div class="p-3">
                <?php echo e($errorLogs->links()); ?>

            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                    <i class="bi bi-check-circle-fill text-success fs-1"></i>
                </div>
                <h5 class="fw-bold text-muted">Sin errores</h5>
                <p class="text-muted mb-0">No se encontraron errores con los filtros seleccionados.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<?php $__currentLoopData = $errorLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="errorModal<?php echo e($log->id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div>
                    <span class="badge bg-<?php echo e($log->level_color); ?> bg-opacity-10 text-<?php echo e($log->level_color); ?> rounded-pill text-uppercase mb-2" style="font-size:.7rem;">
                        <i class="bi <?php echo e($log->source_icon); ?> me-1"></i><?php echo e($log->level); ?> &middot; <?php echo e(ucfirst($log->source)); ?>

                    </span>
                    <h5 class="fw-bold mb-0"><?php echo e($log->title); ?></h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Instancia</small>
                        <span class="fw-bold"><?php echo e($log->tenant?->nombre ?? '—'); ?></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Fecha</small>
                        <span><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Usuario</small>
                        <span><?php echo e($log->user?->name ?? '—'); ?> <?php echo e($log->user?->email ? '(' . $log->user->email . ')' : ''); ?></span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">IP</small>
                        <span><?php echo e($log->ip_address ?? '—'); ?></span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Estado</small>
                        <?php if($log->resolved): ?>
                            <span class="badge bg-success rounded-pill">
                                <i class="bi bi-check-circle me-1"></i>Resuelto
                                <?php if($log->resolvedBy): ?>
                                    por <?php echo e($log->resolvedBy->name); ?>

                                <?php endif; ?>
                                <?php if($log->resolved_at): ?>
                                    <?php echo e($log->resolved_at->format('d/m/Y H:i')); ?>

                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                <i class="bi bi-clock me-1"></i>Pendiente
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if($log->file): ?>
                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block" style="font-size:.6rem;">Archivo</small>
                    <code class="small"><?php echo e($log->file); ?><?php if($log->line): ?>:<?php echo e($log->line); ?><?php endif; ?></code>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">Mensaje Completo</small>
                    <div class="p-3 bg-light rounded-3">
                        <pre class="mb-0 small" style="white-space:pre-wrap;word-break:break-word;"><?php echo e($log->message); ?></pre>
                    </div>
                </div>

                <?php if($log->context): ?>
                <div class="mb-3">
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">Contexto</small>
                    <div class="trace-block">
<?php $__currentLoopData = $log->context; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<span class="context-key"><?php echo e($key); ?></span>: <span class="context-value"><?php echo e(is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $value); ?></span>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if($log->user_agent): ?>
                <div>
                    <small class="text-muted fw-bold text-uppercase d-block mb-2" style="font-size:.6rem;">User Agent</small>
                    <small class="text-muted"><?php echo e($log->user_agent); ?></small>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0">
                <?php if($log->tenant_id): ?>
                <form method="POST" action="<?php echo e(route('owner.instances.errors.resolve', [$log->tenant_id, $log])); ?>" class="me-auto">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn <?php echo e($log->resolved ? 'btn-warning' : 'btn-success'); ?> rounded-pill resolve-btn">
                        <i class="bi <?php echo e($log->resolved ? 'bi-arrow-counterclockwise' : 'bi-check-lg'); ?> me-1"></i>
                        <?php echo e($log->resolved ? 'Reabrir' : 'Marcar como resuelto'); ?>

                    </button>
                </form>
                <?php endif; ?>
                <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/errors/index.blade.php ENDPATH**/ ?>