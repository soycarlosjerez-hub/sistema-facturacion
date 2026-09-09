<?php $__env->startSection('title', 'Dashboard Alquileres'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }
    .stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1.2; }
    .stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">
    <div class="ui-header mb-4" style="--delay:0s; background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-building"></i></div>
                <div>
                    <h4 class="ui-header-title">Alquileres</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-info-circle me-1"></i>
                        Gesti&oacute;n de viviendas, inquilinos, contratos y pagos de alquiler
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.05s">
                <div class="ui-card-accent indigo"></div>
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background:rgba(99,102,241,.1);color:#6366f1;"><i class="bi bi-house-door"></i></div>
                        <div>
                            <div class="ui-stat-value"><?php echo e($stats['total_viviendas']); ?></div>
                            <div class="ui-stat-label">Viviendas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.1s">
                <div class="ui-card-accent emerald"></div>
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background:rgba(16,185,129,.1);color:#10b981;"><i class="bi bi-check-circle"></i></div>
                        <div>
                            <div class="ui-stat-value"><?php echo e($stats['disponibles']); ?></div>
                            <div class="ui-stat-label">Disponibles</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.15s">
                <div class="ui-card-accent amber"></div>
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background:rgba(245,158,11,.1);color:#f59e0b;"><i class="bi bi-file-earmark-text"></i></div>
                        <div>
                            <div class="ui-stat-value"><?php echo e($stats['contratos_activos']); ?></div>
                            <div class="ui-stat-label">Contratos Activos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ui-stat" style="--delay:.2s">
                <div class="ui-card-accent rose"></div>
                <div class="ui-stat-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon" style="background:rgba(244,63,94,.1);color:#f43f5e;"><i class="bi bi-cash-coin"></i></div>
                        <div>
                            <div class="ui-stat-value">RD$ <?php echo e(number_format($stats['pagos_mes'], 2)); ?></div>
                            <div class="ui-stat-label">Cobrado este Mes</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="ui-card" style="--delay:.2s;">
                <div class="ui-card-accent indigo"></div>
                <div class="ui-card-title">
                    <i class="bi bi-lightning"></i>
                    Acceso R&aacute;pido
                </div>
                <div class="ui-card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('alquileres.viviendas.create')); ?>" class="ui-btn ui-btn-ghost text-start rounded-pill py-2">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Vivienda
                        </a>
                        <a href="<?php echo e(route('alquileres.inquilinos.create')); ?>" class="ui-btn ui-btn-ghost text-start rounded-pill py-2">
                            <i class="bi bi-person-plus me-2"></i>Nuevo Inquilino
                        </a>
                        <a href="<?php echo e(route('alquileres.contratos.create')); ?>" class="ui-btn ui-btn-ghost text-start rounded-pill py-2">
                            <i class="bi bi-file-earmark-plus me-2"></i>Nuevo Contrato
                        </a>
                        <a href="<?php echo e(route('alquileres.pagos.create')); ?>" class="ui-btn ui-btn-ghost text-start rounded-pill py-2">
                            <i class="bi bi-cash-coin me-2"></i>Registrar Pago
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.3s;">
                <div class="ui-card-accent amber"></div>
                <div class="ui-card-title">
                    <i class="bi bi-exclamation-triangle" style="color:#f59e0b;"></i>
                    Pr&oacute;ximos Vencimientos
                </div>
                <div class="ui-card-subtitle">Contratos que vencen en los pr&oacute;ximos 30 d&iacute;as</div>
                <div class="ui-card-body p-0">
                    <?php if($proximosVencimientos->isEmpty()): ?>
                        <div class="ui-empty-state">
                            <i class="bi bi-calendar-check"></i>
                            <p>No hay contratos pr&oacute;ximos a vencer</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="ui-table">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Vivienda</th>
                                        <th>Inquilino</th>
                                        <th>Inicio</th>
                                        <th>Vence</th>
                                        <th class="pe-4">D&iacute;as</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $proximosVencimientos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $dias = now()->diffInDays($c->fecha_fin, false); ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold"><?php echo e($c->vivienda->nombre); ?></td>
                                            <td><?php echo e($c->inquilino->nombre); ?></td>
                                            <td><?php echo e($c->fecha_inicio->format('d/m/Y')); ?></td>
                                            <td><?php echo e($c->fecha_fin->format('d/m/Y')); ?></td>
                                            <td class="pe-4">
                                                <?php if($dias <= 0): ?>
                                                    <span class="ui-badge ui-badge-danger">Vencido</span>
                                                <?php elseif($dias <= 7): ?>
                                                    <span class="ui-badge ui-badge-warning"><?php echo e($dias); ?> d&iacute;as</span>
                                                <?php else: ?>
                                                    <span class="ui-badge ui-badge-info"><?php echo e($dias); ?> d&iacute;as</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/alquileres/index.blade.php ENDPATH**/ ?>