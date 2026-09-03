<?php $__env->startSection('title', 'Auditoría'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.btn-icon-hover {
    width: 32px; height: 32px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 50% !important;
    padding: 0;
    transition: all 0.2s;
}
.btn-icon-hover:hover { transform: scale(1.15); }
.status-badge {
    padding: 0.4em 0.8em;
    border-radius: 2rem;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
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
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Registro de Auditoría</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-list-ul me-1"></i>
                        <span>Trazabilidad de acciones en el sistema</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card mb-4" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="card-body p-3">
            <form method="GET" action="<?php echo e(route('audit-logs.index')); ?>" class="row g-2 align-items-center">
                <div class="col-lg-3">
                    <div class="ui-input-group">
                        <span class="ui-input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="ui-input" placeholder="Buscar acción..." value="<?php echo e(request('search')); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select name="action" class="ui-select">
                        <option value="">Todas las acciones</option>
                        <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($a); ?>" <?php echo e(request('action') === $a ? 'selected' : ''); ?>><?php echo e(ucfirst($a)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <select name="model" class="ui-select">
                        <option value="">Todos los módulos</option>
                        <?php $__currentLoopData = $models->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($m); ?>" <?php echo e(request('model') === $m ? 'selected' : ''); ?>><?php echo e($m); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <input type="date" name="desde" class="ui-input" value="<?php echo e(request('desde')); ?>" placeholder="Desde">
                </div>
                <div class="col-lg-2">
                    <input type="date" name="hasta" class="ui-input" value="<?php echo e(request('hasta')); ?>" placeholder="Hasta">
                </div>
                <div class="col-lg-1 d-flex gap-1">
                    <button type="submit" class="ui-btn ui-btn-solid rounded-pill flex-grow-1"><i class="bi bi-funnel"></i></button>
                    <a href="<?php echo e(route('audit-logs.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card overflow-hidden" style="--delay:.2s">
        <div class="ui-card-accent"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: rgba(15,23,42,0.03);">
                    <tr style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;">
                        <th class="ps-4 py-3 text-muted fw-bold">Fecha/Hora</th>
                        <th class="py-3 text-muted fw-bold">Usuario</th>
                        <th class="py-3 text-muted fw-bold">Acción</th>
                        <th class="py-3 text-muted fw-bold">Módulo</th>
                        <th class="py-3 text-muted fw-bold">Descripción</th>
                        <th class="py-3 text-muted fw-bold">IP</th>
                        <th class="text-end pe-4 py-3 text-muted fw-bold">Detalle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="ps-4">
                                <span class="small fw-semibold"><?php echo e($log->created_at->format('d/m/Y')); ?></span>
                                <br><small class="text-muted"><?php echo e($log->created_at->format('h:i A')); ?></small>
                            </td>
                            <td>
                                <span class="fw-semibold small"><?php echo e($log->user?->name ?? '—'); ?></span>
                            </td>
                            <td>
                                <?php
                                    $badge = match($log->action) {
                                        'created' => 'success',
                                        'updated' => 'warning',
                                        'deleted' => 'danger',
                                        default => 'info'
                                    };
                                ?>
                                <span class="status-badge bg-<?php echo e($badge); ?> bg-opacity-10 text-<?php echo e($badge); ?>"><?php echo e(ucfirst($log->action)); ?></span>
                            </td>
                            <td>
                                <span class="small text-muted"><?php echo e(class_basename($log->model_type)); ?></span>
                            </td>
                            <td>
                                <span class="small"><?php echo e(Str::limit($log->description, 80)); ?></span>
                            </td>
                            <td><small class="text-muted font-monospace"><?php echo e($log->ip_address); ?></small></td>
                            <td class="text-end pe-4">
                                <a href="<?php echo e(route('audit-logs.show', $log)); ?>" class="btn btn-sm btn-outline-info rounded-pill btn-icon-hover" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2 mb-0">Sin registros de auditoría</p>
                                <small>Las acciones comenzarán a registrarse automáticamente</small>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="border-0 py-3 px-4" style="background:transparent;">
            <?php echo e($logs->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/audit_logs/index.blade.php ENDPATH**/ ?>