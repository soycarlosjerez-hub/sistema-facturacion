<?php $__env->startSection('title', 'Detalle de Auditoría'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <h4 class="ui-header-title">Detalle de Auditoría</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-hash me-1"></i>
                        <span>#<?php echo e($auditLog->id); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('audit-logs.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="fw-bold mb-4"><i class="bi bi-clock-history me-2" style="color:#64748b;"></i> Información</h5>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Descripción</small>
                            <span class="fw-semibold"><?php echo e($auditLog->description); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Acción</small>
                            <div>
                                <?php $badge = match($auditLog->action) { 'created' => 'success', 'updated' => 'warning', 'deleted' => 'danger', default => 'info' }; ?>
                                <span class="badge bg-<?php echo e($badge); ?> bg-opacity-10 text-<?php echo e($badge); ?> rounded-pill px-3 py-1"><?php echo e(ucfirst($auditLog->action)); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Módulo</small>
                            <span class="fw-semibold"><?php echo e(class_basename($auditLog->model_type)); ?> #<?php echo e($auditLog->model_id); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Modelo completo</small>
                            <span class="font-monospace text-muted small"><?php echo e($auditLog->model_type); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Usuario</small>
                            <span class="fw-semibold"><?php echo e($auditLog->user?->name ?? '—'); ?> (#<?php echo e($auditLog->user_id); ?>)</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Fecha/Hora</small>
                            <span class="fw-semibold"><?php echo e($auditLog->created_at->format('d/m/Y h:i:s A')); ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">Dirección IP</small>
                            <span class="font-monospace"><?php echo e($auditLog->ip_address); ?></span>
                        </div>
                    </div>
                    <?php if($auditLog->user_agent): ?>
                    <div class="col-sm-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">User Agent</small>
                            <span class="font-monospace text-muted small" style="word-break:break-all;"><?php echo e($auditLog->user_agent); ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <?php if($auditLog->old_values && count($auditLog->old_values) > 0): ?>
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-circle me-2" style="color:#64748b;"></i> Valores Anteriores</h6>
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;"><?php echo e(json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                </div>
            </div>
            <?php endif; ?>

            <?php if($auditLog->new_values && count($auditLog->new_values) > 0): ?>
            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-arrow-right-circle me-2" style="color:#64748b;"></i> Valores Nuevos</h6>
                    <pre class="mb-0" style="font-size:.75rem;max-height:300px;overflow-y:auto;"><?php echo e(json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/audit_logs/show.blade.php ENDPATH**/ ?>