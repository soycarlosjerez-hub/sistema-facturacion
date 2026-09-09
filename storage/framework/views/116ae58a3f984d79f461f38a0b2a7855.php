<?php $__env->startSection('title', 'Config. Backups'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-database-fill-gear"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Configuración de Backups</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-sliders me-1"></i>
                        <span>Información del sistema de respaldo</span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('backups.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm ui-btn-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ui-card h-100" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-database"></i> Información del Sistema
                </div>
                <div class="ui-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Directorio de backups</small>
                            <code class="small text-break"><?php echo e($backupDir); ?></code>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Ruta mysqldump</small>
                            <code class="small"><?php echo e($mysqldumpPath); ?></code>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Base de datos</small>
                            <code><?php echo e(config('database.connections.mysql.database')); ?></code>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total backups</small>
                            <span class="fw-semibold"><?php echo e($backupCount); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Espacio total usado</small>
                            <span class="fw-semibold">
                                <?php
                                    $bytes = $totalSize;
                                    $units = ['B','KB','MB','GB'];
                                    $i = 0;
                                    while($bytes>=1024 && $i<3) { $bytes/=1024; $i++; }
                                ?>
                                <?php echo e(round($bytes, 2)); ?> <?php echo e($units[$i]); ?>

                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Último backup</small>
                            <?php if($lastBackup): ?>
                                <span class="fw-semibold"><?php echo e($lastBackup->created_at->format('d/m/Y h:i A')); ?></span>
                                <br><small class="text-muted"><?php echo e($lastBackup->filename); ?></small>
                            <?php else: ?>
                                <span class="text-muted">Nunca</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Retención automática</small>
                            <span class="fw-semibold">30 días</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="ui-card mb-4" style="--delay:.15s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-clock"></i> Backup Automático (Cron)
                </div>
                <div class="ui-card-subtitle">Para activar backups automáticos diarios, agrega esta tarea al cron del servidor</div>
                <div class="ui-card-body">
                    <div class="bg-dark text-white rounded-3 p-3 mb-3">
                        <code style="color:#a5f3fc; font-size:.85rem;">
                            * * * * * cd <?php echo e(base_path()); ?> && php artisan schedule:run >> /dev/null 2>&1
                        </code>
                    </div>
                    <p class="text-muted small mb-0">
                        Una vez configurado el cron, Laravel ejecutará <code class="text-dark bg-light px-1 rounded">backup:run</code> automáticamente cada día a medianoche.
                        Los backups con más de 30 días se eliminarán automáticamente.
                    </p>
                </div>
            </div>

            <div class="ui-card" style="--delay:.2s">
                <div class="ui-card-accent"></div>
                <div class="ui-card-title">
                    <i class="bi bi-terminal"></i> Comandos Artisan
                </div>
                <div class="ui-card-body">
                    <div class="mb-3">
                        <label class="small fw-semibold text-muted d-block mb-1">Backup manual:</label>
                        <code class="small bg-light p-2 rounded d-block">php artisan backup:run --type=manual</code>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-semibold text-muted d-block mb-1">Backup automático:</label>
                        <code class="small bg-light p-2 rounded d-block">php artisan backup:run --type=automatico</code>
                    </div>
                    <div>
                        <label class="small fw-semibold text-muted d-block mb-1">Probar conexión mysqldump:</label>
                        <code class="small bg-light p-2 rounded d-block">"<?php echo e($mysqldumpPath); ?>" --version</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/backups/config.blade.php ENDPATH**/ ?>