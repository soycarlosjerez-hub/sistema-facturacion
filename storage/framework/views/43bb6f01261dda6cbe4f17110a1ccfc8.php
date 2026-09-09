<?php $__env->startSection('title', 'Rol: ' . $role->name); ?>

<?php
    use Spatie\Permission\Models\Permission;
    $modulosIconos = [
        'dashboard' => 'bi-speedometer2',
        'reportes'  => 'bi-graph-up',
        'ventas'    => 'bi-cart-check',
        'cajas'     => 'bi-cash-coin',
        'clientes'  => 'bi-people',
        'cobros'    => 'bi-credit-card',
        'productos' => 'bi-box-seam',
        'compras'   => 'bi-cart-plus',
        'proveedores' => 'bi-truck',
        'almacenes' => 'bi-building',
        'kardex'    => 'bi-clipboard-data',
        'ncf'       => 'bi-receipt',
        'configuracion' => 'bi-gear',
        'usuarios'  => 'bi-shield-lock',
        'roles'     => 'bi-shield-shaded',
    ];
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',     'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',   'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor',  'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',   'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador',  'desc' => 'Reportes y consulta fiscal.'],
    ];
    $cfg = $rolConfig[$role->name] ?? ['color' => '#64748b', 'gradient' => 'linear-gradient(135deg, #64748b 0%, #475569 100%)', 'icon' => 'bi-shield', 'label' => ucfirst($role->name), 'desc' => 'Rol personalizado.'];
    $totalPerms = Permission::count();
    $pct = $totalPerms > 0 ? round(($role->permissions->count() / $totalPerms) * 100) : 0;
?>

<?php echo $__env->make('roles._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    body.dark-mode .role-big-card { background: rgba(30,41,59,.95); }
    body.dark-mode .role-big-card .role-name { color: #f1f5f9; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 ui-page" style="--accent:<?php echo e($cfg['color']); ?>;--accent-rgb:<?php echo e(implode(',', sscanf($cfg['color'], '#%02x%02x%02x'))); ?>;--accent-hover:<?php echo e($cfg['color']); ?>;">
    <div class="ui-header" style="background: linear-gradient(135deg, <?php echo e($cfg['color']); ?>, <?php echo e($cfg['color']); ?>cc, <?php echo e($cfg['color']); ?>99, <?php echo e($cfg['color']); ?>);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body w-100">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi <?php echo e($cfg['icon']); ?>"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            ROL
                        </span>
                        <?php if($isProtected): ?>
                            <span class="protected-badge"><i class="bi bi-lock-fill"></i>Protegido</span>
                        <?php endif; ?>
                    </div>
                    <h2 class="ui-header-title mb-0"><?php echo e($cfg['label']); ?></h2>
                    <div class="ui-header-meta"><?php echo e($cfg['desc']); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route($routePrefix . 'roles.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
                <a href="<?php echo e(route($routePrefix . 'roles.edit', $role)); ?>" class="ui-btn ui-btn-ghost rounded-pill px-3 fw-bold">
                    <i class="bi bi-pencil me-1"></i>Editar
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble" style="background: <?php echo e($cfg['color']); ?>20; color: <?php echo e($cfg['color']); ?>;">
                        <i class="bi bi-key"></i>
                    </div>
                    <div>
                        <div class="stat-label">Permisos</div>
                        <div class="stat-value" style="color: <?php echo e($cfg['color']); ?>;"><?php echo e($role->permissions->count()); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-info bg-opacity-10 text-info">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div class="stat-label">Cobertura</div>
                        <div class="stat-value"><?php echo e($pct); ?>%</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="stat-label">Usuarios</div>
                        <div class="stat-value text-success"><?php echo e($users->count()); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="ui-stat">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-bubble bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-folder"></i>
                    </div>
                    <div>
                        <div class="stat-label">Módulos</div>
                        <div class="stat-value"><?php echo e($permisosGrouped->count()); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="ui-card-title"><i class="bi bi-key icon-purple"></i>Permisos Asignados</h5>
                            <small class="ui-card-subtitle">Acciones permitidas para los usuarios con este rol</small>
                        </div>
                        <div class="ui-input-group" style="max-width: 280px;">
                            <span class="ui-input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="permFilter" class="ui-input border-0 bg-light" placeholder="Filtrar permisos...">
                        </div>
                    </div>
                    <?php $__empty_0 = true; $__currentLoopData = $permisosGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="perm-module-card mb-3" data-text="<?php echo e(strtolower($modulo)); ?>" style="--accent-color: <?php echo e($cfg['color']); ?>;">
                            <div class="module-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="module-icon">
                                        <i class="bi <?php echo e($modulosIconos[$modulo] ?? 'bi-folder'); ?>"></i>
                                    </div>
                                    <div class="module-title"><?php echo e(ucfirst($modulo)); ?></div>
                                </div>
                                <span class="badge bg-light text-dark"><?php echo e($perms->count()); ?></span>
                            </div>
                            <div class="row g-2">
                                <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $action = explode('.', $p->name)[1] ?? '';
                                        $cls = in_array($action, ['delete','destroy','anular']) ? 'delete' : (in_array($action, ['create','store','update','edit','anular','abrir','cerrar']) ? 'write' : '');
                                    ?>
                                    <div class="col-md-6">
                                        <div class="perm-toggle is-checked perm-filterable" data-text="<?php echo e(strtolower($p->name)); ?>">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                            <span class="perm-name"><?php echo e(str_replace($modulo.'.', '', $p->name)); ?></span>
                                            <span class="badge bg-light text-muted" style="font-size: 0.65rem;"><?php echo e($cls ?: 'read'); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x display-4 d-block mb-2"></i>
                            <p class="mb-0">Este rol no tiene permisos asignados.</p>
                            <a href="<?php echo e(route($routePrefix . 'roles.edit', $role)); ?>" class="ui-btn ui-btn-solid rounded-pill px-4 mt-3">
                                <i class="bi bi-pencil me-1"></i>Asignar Permisos
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ui-card">
                <div class="ui-card-accent"></div>
                <div class="card-body">
                    <h5 class="ui-card-title"><i class="bi bi-people icon-purple"></i>Usuarios Asignados</h5>
                    <small class="ui-card-subtitle"><?php echo e($users->count()); ?> <?php echo e($users->count() == 1 ? 'persona' : 'personas'); ?> con este rol</small>
                    <?php $__empty_0 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3">
                            <div class="user-avatar" style="width: 40px; height: 40px; border-radius: 12px; background: <?php echo e($cfg['gradient']); ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1))); ?>

                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark"><?php echo e($user->name); ?></div>
                                <small class="text-muted"><?php echo e($user->email); ?></small>
                            </div>
                            <?php if(!request()->routeIs('owner.*')): ?>
                            <a href="<?php echo e(route('usuarios.show', $user->id)); ?>" class="text-decoration-none">
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-person-x display-6 d-block mb-2"></i>
                            <p class="mb-0">Ningún usuario con este rol todavía.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('permFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.perm-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/roles/show.blade.php ENDPATH**/ ?>