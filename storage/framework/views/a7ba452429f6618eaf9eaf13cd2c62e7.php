<?php $__env->startSection('title', 'Editar Rol: ' . $role->name); ?>

<?php
    use Spatie\Permission\Models\Permission;
    $modulosIconos = [
        'dashboard' => 'bi-speedometer2', 'reportes' => 'bi-graph-up', 'ventas' => 'bi-cart-check',
        'cajas' => 'bi-cash-coin', 'clientes' => 'bi-people', 'cobros' => 'bi-credit-card',
        'productos' => 'bi-box-seam', 'compras' => 'bi-cart-plus', 'proveedores' => 'bi-truck',
        'almacenes' => 'bi-building', 'kardex' => 'bi-clipboard-data', 'ncf' => 'bi-receipt',
        'configuracion' => 'bi-gear', 'usuarios' => 'bi-shield-lock', 'roles' => 'bi-shield-shaded',
    ];
    $modulosColores = [
        'dashboard' => '#38bdf8', 'reportes' => '#a855f7', 'ventas' => '#22c55e', 'cajas' => '#f59e0b',
        'clientes' => '#ec4899', 'cobros' => '#06b6d4', 'productos' => '#3b82f6', 'compras' => '#f97316',
        'proveedores' => '#84cc16', 'almacenes' => '#10b981', 'kardex' => '#14b8a6', 'ncf' => '#ef4444',
        'configuracion' => '#64748b', 'usuarios' => '#6366f1', 'roles' => '#4f46e5',
    ];
    $rolConfig = [
        'admin'    => ['color' => '#ef4444', 'gradient' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', 'icon' => 'bi-shield-lock-fill',  'label' => 'Admin',    'desc' => 'Acceso total al sistema.'],
        'gerente'  => ['color' => '#f59e0b', 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-person-badge-fill', 'label' => 'Gerente',  'desc' => 'Gestión operativa, sin admin.'],
        'vendedor' => ['color' => '#38bdf8', 'gradient' => 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)', 'icon' => 'bi-cart-check-fill',  'label' => 'Vendedor', 'desc' => 'POS, ventas y caja.'],
        'almacen'  => ['color' => '#22c55e', 'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)', 'icon' => 'bi-box-seam-fill',     'label' => 'Almacén',  'desc' => 'Productos, compras, stock.'],
        'contador' => ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-calculator-fill',   'label' => 'Contador', 'desc' => 'Reportes y consulta fiscal.'],
    ];
    $cfg = $rolConfig[$role->name] ?? ['color' => '#6366f1', 'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)', 'icon' => 'bi-shield', 'label' => ucfirst($role->name), 'desc' => 'Rol personalizado.'];
    $oldPerms = old('permissions', $permisosAsignados);
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
<div class="container-fluid px-4 ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">
    <div class="ui-header" style="background: linear-gradient(135deg, #f59e0b, #ea580c, #d97706, #f59e0b);">
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
                            <i class="bi bi-pencil-square me-1"></i>EDITANDO
                        </span>
                        <?php if($isProtected): ?>
                            <span class="protected-badge"><i class="bi bi-lock-fill"></i>Protegido</span>
                        <?php endif; ?>
                    </div>
                    <h2 class="ui-header-title mb-0"><?php echo e($role->name); ?></h2>
                    <div class="ui-header-meta"><?php echo e($permisosAsignados ? count($permisosAsignados) . ' permisos asignados' : 'Sin permisos'); ?></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route($routePrefix . 'roles.show', $role)); ?>" class="ui-btn ui-btn-primary rounded-pill px-3">
                    <i class="bi bi-eye me-1"></i>Ver
                </a>
                <a href="<?php echo e(route($routePrefix . 'roles.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($isProtected): ?>
        <div class="alert rounded-4 border-0 shadow-sm mb-3 d-flex align-items-center" style="background: rgba(245,158,11,0.1); border-left: 4px solid #f59e0b !important;">
            <i class="bi bi-shield-exclamation text-warning fs-4 me-3"></i>
            <div>
                <strong>Rol Protegido.</strong> El nombre y los permisos de <strong><?php echo e($role->name); ?></strong> están protegidos para garantizar el acceso completo al sistema. Solo puedes renombrar el rol.
            </div>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route($routePrefix . 'roles.update', $role)); ?>" method="POST" id="roleForm">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="ui-card mb-3">
                    <div class="ui-card-accent"></div>
                    <div class="card-body">
                        <h5 class="ui-card-title"><i class="bi bi-tag icon-purple"></i>Nombre del Rol</h5>
                        <div class="form-floating-modern">
                            <i class="bi bi-shield form-icon"></i>
                            <input type="text" name="name" id="name" class="ui-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('name', $role->name)); ?>" placeholder=" " required maxlength="50" pattern="[a-z0-9_\-]+">
                            <label class="form-label-float" for="name">Nombre del rol</label>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1 ms-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <?php if($role->users()->count() > 0): ?>
                            <div class="alert alert-warning rounded-3 mt-3 mb-0 d-flex align-items-center gap-2 small">
                                <i class="bi bi-info-circle"></i>
                                <span><?php echo e($role->users()->count()); ?> usuario(s) usan este rol. El cambio de nombre los actualizará automáticamente.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if(!$isProtected): ?>
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body">
                        <h6 class="ui-card-title"><i class="bi bi-stars icon-purple"></i>Plantillas Rápidas</h6>
                        <div class="d-grid gap-2">
                            <button type="button" class="ui-btn ui-btn-ghost text-start rounded-3 py-2 perm-template" data-template="readonly">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-eye fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Solo Lectura</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Solo permisos .view</small>
                                    </div>
                                </div>
                            </button>
                            <button type="button" class="ui-btn ui-btn-ghost text-start rounded-3 py-2 perm-template" data-template="all">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-check-all fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Todos los permisos</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Acceso completo</small>
                                    </div>
                                </div>
                            </button>
                            <button type="button" class="ui-btn ui-btn-ghost text-start rounded-3 py-2 perm-template" data-template="clear">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-x-lg fs-5"></i>
                                    <div>
                                        <div class="fw-bold">Limpiar selección</div>
                                        <small class="text-muted" style="font-size: 0.7rem;">Empezar desde cero</small>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="ui-card mt-3">
                    <div class="ui-card-accent"></div>
                    <div class="card-body text-center">
                        <div class="stat-label">Permisos Seleccionados</div>
                        <div class="stat-value" style="color: #8b5cf6;"><?php echo e(count($oldPerms)); ?></div>
                        <small class="text-muted">de <?php echo e(Permission::count()); ?> disponibles</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div>
                                <h5 class="ui-card-title"><i class="bi bi-key icon-purple"></i>Permisos</h5>
                                <small class="ui-card-subtitle">Modifica los permisos asignados a este rol</small>
                            </div>
                            <div class="ui-input-group" style="max-width: 280px;">
                                <span class="ui-input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                                <input type="text" id="permFilter" class="ui-input border-0 bg-light" placeholder="Buscar permiso...">
                            </div>
                        </div>
                        <?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="perm-module-card mb-3 perm-filterable" data-text="<?php echo e(strtolower($modulo)); ?>" style="--accent-color: <?php echo e($modulosColores[$modulo] ?? '#38bdf8'); ?>;">
                                <div class="module-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="module-icon">
                                            <i class="bi <?php echo e($modulosIconos[$modulo] ?? 'bi-folder'); ?>"></i>
                                        </div>
                                        <div class="module-title"><?php echo e(ucfirst($modulo)); ?></div>
                                        <span class="badge bg-light text-muted ms-1"><?php echo e($perms->count()); ?></span>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input module-check" data-module="<?php echo e($modulo); ?>" id="mod-<?php echo e($modulo); ?>" <?php echo e($isProtected ? 'disabled' : ''); ?>>
                                        <label class="form-check-label small fw-bold" for="mod-<?php echo e($modulo); ?>">Todos</label>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $action = explode('.', $p->name)[1] ?? '';
                                            $checked = in_array($p->name, $oldPerms);
                                        ?>
                                        <div class="col-md-6">
                                            <label class="perm-toggle <?php echo e($checked ? 'is-checked' : ''); ?> perm-filterable" data-text="<?php echo e(strtolower($p->name)); ?> <?php echo e(strtolower($modulo)); ?>">
                                                <input type="checkbox" name="permissions[]" value="<?php echo e($p->name); ?>"
                                                       data-module="<?php echo e($modulo); ?>"
                                                       <?php echo e($checked ? 'checked' : ''); ?>

                                                       <?php echo e($isProtected ? 'disabled' : ''); ?>>
                                                <i class="bi <?php echo e($checked ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'); ?>"></i>
                                                <span class="perm-name"><?php echo e(str_replace($modulo.'.', '', $p->name)); ?></span>
                                                <small class="text-muted" style="font-size: 0.65rem;"><?php echo e($p->name); ?></small>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 mb-4 flex-wrap gap-2">
            <?php if($role->name !== 'admin' && $role->users()->count() == 0): ?>
                <form action="<?php echo e(route($routePrefix . 'roles.destroy', $role)); ?>" method="POST" onsubmit="return confirm('¿Eliminar el rol &quot;<?php echo e($role->name); ?>&quot;? Esta acción no se puede deshacer.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="ui-action ui-action-delete rounded-pill px-4">
                        <i class="bi bi-trash me-1"></i>Eliminar Rol
                    </button>
                </form>
            <?php else: ?>
                <div class="text-muted small">
                    <?php if($role->name === 'admin'): ?>
                        <i class="bi bi-shield-check text-success"></i> Rol del sistema, no se puede eliminar
                    <?php else: ?>
                        <i class="bi bi-info-circle"></i> <?php echo e($role->users()->count()); ?> usuario(s) con este rol, no se puede eliminar
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php if(!$isProtected): ?>
<div class="premium-sticky-bar" id="stickySaveBar">
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-primary"></i>
            <span class="fw-semibold d-none d-sm-inline">Editar Rol</span>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" form="roleForm" class="btn-save">
                <i class="bi bi-save me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>

<script>
    const updateCount = () => {
        const n = document.querySelectorAll('input[name="permissions[]"]:checked').length;
        document.getElementById('permCount').textContent = n;
    };

    const updateVisual = (checkbox) => {
        const toggle = checkbox.closest('.perm-toggle');
        const icon = toggle.querySelector('i');
        if (checkbox.checked) {
            toggle.classList.add('is-checked');
            icon.className = 'bi bi-check-circle-fill text-success';
        } else {
            toggle.classList.remove('is-checked');
            icon.className = 'bi bi-circle text-muted';
        }
        updateCount();
    };

    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            updateVisual(cb);
            const mod = cb.dataset.module;
            const allInModule = document.querySelectorAll(`input[data-module="${mod}"]`);
            const checked = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]:checked`);
            const modCheck = document.querySelector(`.module-check[data-module="${mod}"]`);
            if (modCheck) modCheck.checked = allInModule.length === checked.length;
        });
    });

    document.querySelectorAll('.module-check').forEach(mc => {
        mc.addEventListener('change', () => {
            const mod = mc.dataset.module;
            const cbs = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]`);
            cbs.forEach(cb => {
                cb.checked = mc.checked;
                updateVisual(cb);
            });
        });
    });

    document.querySelectorAll('.perm-template').forEach(btn => {
        btn.addEventListener('click', () => {
            const t = btn.dataset.template;
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                if (t === 'all') {
                    cb.checked = true;
                } else if (t === 'clear') {
                    cb.checked = false;
                } else if (t === 'readonly') {
                    const action = cb.value.split('.')[1] || '';
                    cb.checked = action === 'view' || action === 'view.own' || action === 'view.report';
                }
                updateVisual(cb);
            });
            document.querySelectorAll('.module-check').forEach(mc => {
                const mod = mc.dataset.module;
                const all = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]`);
                const checked = document.querySelectorAll(`input[name="permissions[]"][data-module="${mod}"]:checked`);
                mc.checked = all.length === checked.length;
            });
        });
    });

    document.getElementById('permFilter')?.addEventListener('input', function(e) {
        const q = e.target.value.toLowerCase();
        document.querySelectorAll('.perm-filterable').forEach(el => {
            el.style.display = el.dataset.text.includes(q) ? '' : 'none';
        });
    });
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/roles/edit.blade.php ENDPATH**/ ?>