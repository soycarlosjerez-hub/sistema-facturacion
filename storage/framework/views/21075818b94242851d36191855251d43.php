<?php $__env->startSection('title', 'Crear Usuario'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .premium-header-amber {
        background: linear-gradient(135deg, #f59e0b, #f97316, #f59e0b, #d97706);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(245,158,11,.25);
    }
    .premium-header-amber::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-amber .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-amber .bubble:nth-child(1) {
        width: 80px; height: 80px; top: -20px; right: 10%;
        animation: premiumFloat 4s ease-in-out infinite;
    }
    .premium-header-amber .bubble:nth-child(2) {
        width: 50px; height: 50px; bottom: 10px; right: 28%;
        animation: premiumFloat 5s ease-in-out infinite 1s;
    }
    .premium-header-amber .bubble:nth-child(3) {
        width: 100px; height: 100px; bottom: -30px; right: 5%;
        animation: premiumFloat 6s ease-in-out infinite .5s;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $rolSeleccionado = old('role', 'vendedor');
    $defaultConfig = [
        'color' => '#64748b',
        'gradient' => 'linear-gradient(135deg,#64748b,#475569)',
        'icon' => 'bi-person',
        'label' => 'Rol',
        'desc' => 'Rol personalizado.'
    ];
?>

<?php echo $__env->make('usuarios._rol_config', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('usuarios._styles', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb">

    <div class="premium-header-amber mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="ui-avatar-circle">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-person-plus-fill me-1"></i>NUEVO USUARIO
                    </span>
                    <h4 class="fw-bold mb-1 text-white">Crear Usuario</h4>
                    <small class="text-white opacity-75">Agrega un nuevo miembro al sistema y asigna su nivel de acceso</small>
                </div>
            </div>
            <a href="<?php echo e(route('usuarios.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('usuarios.store')); ?>" method="POST" id="userForm">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="ui-card h-100" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-person-vcard icon-amber"></i> Información del Usuario</div>
                    <div class="card-body p-4">
                        <?php echo $__env->make('usuarios._form_fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                </div>
            </div>

            <div class="col-lg-5">
                <div class="ui-card h-100" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-shield-fill-check icon-amber"></i> Asignar Rol</div>
                    <div class="premium-card-subtitle">Selecciona el nivel de acceso del usuario</div>
                    <div class="card-body p-4">
                        <div class="role-picker" id="rolePicker">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $cfg = $rolConfig[$rol->name] ?? $defaultConfig;
                                    $cfg['label'] = $cfg['label'] ?? ucfirst(str_replace(['-', '_'], ' ', $rol->name));
                                    $cfg['desc'] = $cfg['desc'] ?? 'Rol personalizado.';
                                    $cfg['color'] = $cfg['color'] ?? '#64748b';
                                    $cfg['gradient'] = $cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)';
                                    $cfg['icon'] = $cfg['icon'] ?? 'bi-person';
                                ?>
                                <label class="role-card <?php echo e($rolSeleccionado == $rol->name ? 'active' : ''); ?>"
                                       style="--role-color: <?php echo e($cfg['color']); ?>; --role-gradient: <?php echo e($cfg['gradient']); ?>;">
                                    <input type="radio" name="role" value="<?php echo e($rol->name); ?>"
                                           <?php echo e($rolSeleccionado == $rol->name ? 'checked' : ''); ?> required>
                                    <div class="role-icon" style="background: <?php echo e($cfg['gradient']); ?>;">
                                        <i class="bi <?php echo e($cfg['icon']); ?>"></i>
                                    </div>
                                    <div class="role-name"><?php echo e($cfg['label']); ?></div>
                                    <div class="role-desc"><?php echo e($cfg['desc']); ?></div>
                                    <div class="role-perms"><i class="bi bi-key"></i> <?php echo e($rol->permissions->count()); ?> permisos</div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="ui-card mt-3" id="permPreviewCard" style="--delay:.3s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-key icon-amber"></i> Vista previa de permisos</div>
                    <div class="premium-card-subtitle">Estos son los accesos que tendrá el usuario</div>
                    <div class="card-body p-4">
                        <div class="permission-preview" id="permPreview">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $grouped = $rol->permissions->groupBy(function($p) { return explode('.', $p->name)[0]; });
                                ?>
                                <div class="perm-block" data-role="<?php echo e($rol->name); ?>" style="display: <?php echo e($rolSeleccionado == $rol->name ? 'block' : 'none'); ?>;">
                                    <?php $__empty_0 = true; $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                                        <div class="perm-group">
                                            <div class="perm-group-title"><?php echo e(ucfirst($modulo)); ?></div>
                                            <div>
                                                <?php $__currentLoopData = $perms->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="perm-tag"><i class="bi bi-check2"></i> <?php echo e(str_replace($modulo.'.', '', $p->name)); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($perms->count() > 8): ?>
                                                    <span class="perm-tag" style="background: rgba(15,23,42,0.06); color: #64748b;">+<?php echo e($perms->count() - 8); ?> más</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                                        <div class="text-muted small">Este rol no tiene permisos asignados.</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle" style="color:#f59e0b;"></i>
                <span class="fw-semibold d-none d-sm-inline">Crear Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('usuarios.index')); ?>" class="btn-cancel">Cancelar</a>
                <button type="submit" form="userForm" class="btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.role-card input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', () => {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('active'));
            radio.closest('.role-card').classList.add('active');
            document.querySelectorAll('.perm-block').forEach(b => b.style.display = 'none');
            const target = document.querySelector(`.perm-block[data-role="${radio.value}"]`);
            if (target) target.style.display = 'block';
        });
    });

    const passInput = document.getElementById('password');
    const strengthBar = document.getElementById('strengthBar');
    if (passInput && strengthBar) {
        passInput.addEventListener('input', () => {
            const v = passInput.value;
            let score = 0;
            if (v.length >= 6) score += 25;
            if (v.length >= 10) score += 15;
            if (/[A-Z]/.test(v)) score += 20;
            if (/[0-9]/.test(v)) score += 20;
            if (/[^A-Za-z0-9]/.test(v)) score += 20;
            strengthBar.style.width = score + '%';
            strengthBar.style.background = score < 40 ? '#ef4444' : score < 70 ? '#f59e0b' : '#22c55e';
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/usuarios/create.blade.php ENDPATH**/ ?>