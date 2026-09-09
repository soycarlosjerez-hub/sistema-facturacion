<?php $__env->startSection('title', 'Matriz de Permisos'); ?>

<?php
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
?>

<?php echo $__env->make('roles._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    body.dark-mode .role-big-card { background: rgba(30,41,59,.95); }
    body.dark-mode .role-big-card .role-name { color: #f1f5f9; }
    body.dark-mode .matrix-table th { background: rgba(30,41,59,.95); }
    body.dark-mode .matrix-table .module-row td { background: rgba(15,23,42,.3); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-grid-3x3-gap"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            <i class="bi bi-grid-3x3-gap me-1"></i>VISUALIZACIÓN
                        </span>
                    </div>
                    <h2 class="ui-header-title mb-0">Matriz de Permisos</h2>
                    <div class="ui-header-meta">Vista comparativa de todos los roles y sus permisos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route($routePrefix . 'roles.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
                <a href="<?php echo e(route($routePrefix . 'roles.create')); ?>" class="ui-btn ui-btn-ghost rounded-pill px-3 fw-bold">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card mb-3">
        <div class="ui-card-accent"></div>
        <div class="card-body d-flex flex-wrap gap-3 align-items-center">
            <span class="d-flex align-items-center gap-2 small">
                <span class="perm-check on" style="display: inline-block; width: 22px; height: 22px; border-radius: 6px; background: rgba(34,197,94,0.15); color: #16a34a; text-align: center; line-height: 22px;">
                    <i class="bi bi-check-lg"></i>
                </span>
                <strong>Permitido</strong>
            </span>
            <span class="d-flex align-items-center gap-2 small">
                <span class="perm-check off" style="display: inline-block; width: 22px; height: 22px; border-radius: 6px; background: rgba(239,68,68,0.05); color: #cbd5e1; text-align: center; line-height: 22px;">
                    <i class="bi bi-x"></i>
                </span>
                <strong>No permitido</strong>
            </span>
            <div class="ms-auto small text-muted">
                <i class="bi bi-info-circle me-1"></i>Esta matriz es de solo lectura. Para editar, ve al detalle de cada rol.
            </div>
        </div>
    </div>

    <div class="ui-card">
        <div class="ui-card-accent"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table matrix-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="min-width: 240px;">Permiso</th>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $cfg = $rolConfig[$rol->name] ?? null; ?>
                                <th class="text-center" style="min-width: 110px;">
                                    <a href="<?php echo e(route($routePrefix . 'roles.show', $rol)); ?>" class="text-decoration-none">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white mb-1" style="width: 32px; height: 32px; background: <?php echo e($cfg['gradient'] ?? '#64748b'); ?>;">
                                                <i class="bi <?php echo e($cfg['icon'] ?? 'bi-shield'); ?>"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.7rem; text-transform: uppercase;"><?php echo e(ucfirst($rol->name)); ?></span>
                                        </div>
                                    </a>
                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="module-row">
                                <td colspan="<?php echo e(count($roles) + 1); ?>" class="ps-4">
                                    <i class="bi bi-folder2-open me-2"></i><?php echo e(ucfirst($modulo)); ?>

                                </td>
                            </tr>
                            <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="ps-4 perm-name-cell">
                                        <code class="text-muted"><?php echo e($p->name); ?></code>
                                    </td>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $has = $matrix[$rol->name]->has($p->name); ?>
                                        <td class="perm-cell">
                                            <span class="perm-check <?php echo e($has ? 'on' : 'off'); ?>">
                                                <i class="bi <?php echo e($has ? 'bi-check-lg' : 'bi-x'); ?>"></i>
                                            </span>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/roles/matrix.blade.php ENDPATH**/ ?>