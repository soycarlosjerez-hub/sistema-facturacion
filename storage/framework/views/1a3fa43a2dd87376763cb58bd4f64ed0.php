<?php $__env->startSection('title', 'Crear Usuario'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .premium-header-blue {
        background: linear-gradient(135deg, #3b82f6, #2563eb, #3b82f6, #1d4ed8);
        background-size: 300% 300%;
        animation: premiumGradientShift 6s ease infinite;
        border-radius: 1.2rem;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
        box-shadow: 0 8px 32px rgba(59,130,246,.25);
    }
    .premium-header-blue::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.1) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.07) 0%, transparent 50%);
        pointer-events: none;
    }
    .premium-header-blue .bubble {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        pointer-events: none;
    }
    .premium-header-blue .bubble:nth-child(1) { width: 80px; height: 80px; top: -20px; right: 10%; animation: premiumFloat 4s ease-in-out infinite; }
    .premium-header-blue .bubble:nth-child(2) { width: 50px; height: 50px; bottom: 10px; right: 28%; animation: premiumFloat 5s ease-in-out infinite 1s; }
    .premium-header-blue .bubble:nth-child(3) { width: 100px; height: 100px; bottom: -30px; right: 5%; animation: premiumFloat 6s ease-in-out infinite .5s; }

    .role-card {
        display: flex; align-items: center; gap: 12px; padding: 12px 16px;
        border-radius: 12px; border: 2px solid rgba(0,0,0,.06); background: rgba(255,255,255,.8);
        cursor: pointer; transition: all .2s; margin-bottom: 8px;
    }
    .role-card:hover { border-color: rgba(59,130,246,.3); background: rgba(59,130,246,.04); transform: translateX(4px); }
    .role-card.active { border-color: #3b82f6; background: rgba(59,130,246,.08); box-shadow: 0 4px 12px rgba(59,130,246,.15); }
    body.dark-mode .role-card { border-color: rgba(255,255,255,.08); background: rgba(30,41,59,.9); }
    body.dark-mode .role-card:hover { border-color: rgba(59,130,246,.4); background: rgba(59,130,246,.08); }
    body.dark-mode .role-card.active { border-color: #3b82f6; background: rgba(59,130,246,.15); }
    .role-icon-sm {
        width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; color: white; flex-shrink: 0;
    }
    .role-name { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
    .role-desc { font-size: 0.8rem; color: #64748b; margin: 0; }
    body.dark-mode .role-name { color: #f1f5f9; }
    body.dark-mode .role-desc { color: #94a3b8; }

    .premium-card-title { font-size: 1rem; font-weight: 800; color: #1e293b; }
    body.dark-mode .premium-card-title { color: #f1f5f9; }
    .premium-card-subtitle { font-size: 0.8rem; color: #64748b; margin-top: 4px; }
    body.dark-mode .premium-card-subtitle { color: #94a3b8; }

    .ui-card { background: rgba(255,255,255,.9); border: 1px solid rgba(0,0,0,.06); border-radius: 16px; backdrop-filter: blur(12px); }
    body.dark-mode .ui-card { background: rgba(30,41,59,.9); border-color: rgba(255,255,255,.08); }

    .ui-label { font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    body.dark-mode .ui-label { color: #d1d5db; }
    .ui-input, .ui-select {
        width: 100%; padding: 10px 14px; border: 1.5px solid rgba(0,0,0,.1); border-radius: 10px;
        font-size: 0.9rem; background: rgba(255,255,255,.9); transition: all .2s;
    }
    .ui-input:focus, .ui-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); outline: none; }
    body.dark-mode .ui-input, body.dark-mode .ui-select { background: rgba(15,23,42,.9); border-color: rgba(255,255,255,.15); color: #f1f5f9; }
    body.dark-mode .ui-input:focus, body.dark-mode .ui-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.2); }

    .btn-save { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; padding: 10px 24px; border-radius: 10px; font-weight: 700; transition: all .2s; }
    .btn-save:hover { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,.3); }
    .btn-cancel { background: rgba(255,255,255,.15); color: #64748b; border: 1.5px solid rgba(0,0,0,.08); padding: 10px 20px; border-radius: 10px; font-weight: 600; transition: all .2s; text-decoration: none; }
    .btn-cancel:hover { background: rgba(255,255,255,.2); color: #1e293b; text-decoration: none; }
    body.dark-mode .btn-cancel { background: rgba(255,255,255,.08); color: #94a3b8; border-color: rgba(255,255,255,.12); }
    body.dark-mode .btn-cancel:hover { background: rgba(255,255,255,.12); color: #f1f5f9; }

    .premium-sticky-bar {
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;
        background: rgba(255,255,255,.95); backdrop-filter: blur(12px);
        border-top: 1px solid rgba(0,0,0,.08); padding: 12px 24px;
    }
    body.dark-mode .premium-sticky-bar { background: rgba(15,23,42,.95); border-color: rgba(255,255,255,.1); }

    .strength-bar-container { height: 6px; border-radius: 999px; background: rgba(0,0,0,.06); overflow: hidden; margin-top: 6px; }
    .strength-bar { height: 100%; border-radius: 999px; transition: all .3s; width: 0%; }
    body.dark-mode .strength-bar-container { background: rgba(255,255,255,.1); }

    .role-picker { max-height: 400px; overflow-y: auto; padding-right: 4px; }
    .role-picker::-webkit-scrollbar { width: 4px; }
    .role-picker::-webkit-scrollbar-thumb { background: rgba(0,0,0,.15); border-radius: 4px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="premium-header-blue mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position:relative; z-index:2;">
            <div class="d-flex align-items-center gap-3">
                <div class="ui-avatar-circle" style="background: rgba(255,255,255,.2); backdrop-filter: blur(8px); border: 2px solid rgba(255,255,255,.35);">
                    <i class="bi bi-person-plus"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-building me-1"></i><?php echo e($instance->nombre ?? 'Mi Instancia'); ?>

                    </span>
                    <h4 class="fw-bold mb-1 text-white">Crear Usuario</h4>
                    <small class="text-white opacity-75">Agrega un nuevo miembro a tu equipo y asigna su rol de acceso</small>
                </div>
            </div>
            <a href="<?php echo e(route('instance.users.index', $instance->id)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><i class="bi bi-exclamation-circle me-1"></i><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('instance.users.store', $instance->id)); ?>" method="POST" id="userForm">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="instance_id" value="<?php echo e($instance->id); ?>">

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="ui-card h-100">
                    <div class="card-body p-4">
                        <div class="premium-card-title mb-1"><i class="bi bi-person-vcard me-2"></i>Información del Usuario</div>
                        <div class="premium-card-subtitle mb-4">Completa los datos del nuevo miembro del equipo</div>

                        <div class="mb-3">
                            <label class="ui-label">Nombre Completo</label>
                            <input type="text" name="name" class="ui-input" value="<?php echo e(old('name')); ?>" placeholder="Ej: Juan Pérez" required>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Correo Electrónico</label>
                            <input type="email" name="email" class="ui-input" value="<?php echo e(old('email')); ?>" placeholder="ejemplo@correo.com" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ui-label">Contraseña</label>
                                <input type="password" name="password" class="ui-input" id="password" placeholder="Mínimo 12 caracteres" required>
                                <div class="strength-bar-container">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="ui-label">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" class="ui-input" id="password_confirmation" placeholder="Repite la contraseña" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="ui-label">Rol de Instancia <small class="text-muted">(Opcional)</small></label>
                            <p class="text-muted small mb-3">Selecciona el nivel de acceso del usuario en tu negocio</p>

                            <div class="role-picker" id="rolePicker">
                                <?php if($instanceRoles->count() > 0): ?>
                                    <?php $__currentLoopData = $instanceRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="role-card <?php echo e(old('instance_role_id') == $rol->id ? 'active' : ''); ?>" data-role-id="<?php echo e($rol->id); ?>">
                                            <input type="radio" name="instance_role_id" value="<?php echo e($rol->id); ?>" class="d-none" <?php echo e(old('instance_role_id') == $rol->id ? 'checked' : ''); ?>>
                                            <div class="role-icon-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                                <i class="bi bi-shield-check"></i>
                                            </div>
                                            <div>
                                                <div class="role-name"><?php echo e(ucfirst($rol->name)); ?></div>
                                                <?php if($rol->users->count() > 0): ?>
                                                    <div class="role-desc"><?php echo e($rol->users->count()); ?> usuario(s) con este rol</div>
                                                <?php else: ?>
                                                    <div class="role-desc">Rol sin usuarios asignados</div>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="bi bi-shield-lock display-4 text-muted d-block mb-3"></i>
                                        <p class="text-muted mb-0">No hay roles de instancia disponibles.</p>
                                        <small class="text-muted">Contacta al administrador del sistema para que configure roles.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php $__errorArgs = ['instance_role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ui-card">
                    <div class="card-body p-4">
                        <div class="premium-card-title mb-1"><i class="bi bi-info-circle me-2"></i>Resumen</div>
                        <div class="premium-card-subtitle mb-3">Información del nuevo usuario</div>

                        <div class="text-center mb-4">
                            <div class="user-avatar-sm mx-auto mb-3" style="background: linear-gradient(135deg, #3b82f6, #2563eb); width: 64px; height: 64px; font-size: 1.5rem;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="fw-bold" id="summaryName">Sin nombre</div>
                            <div class="text-muted small" id="summaryEmail">sin email</div>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Instancia</label>
                            <div class="instance-chip">
                                <i class="bi bi-building"></i> <?php echo e($instance->nombre); ?>

                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="ui-label">Rol Asignado</label>
                            <div id="summaryRole" class="badge bg-light text-dark rounded-pill px-3 py-2">
                                <i class="bi bi-person-x me-1"></i> Sin asignar
                            </div>
                        </div>

                        <div class="alert alert-info rounded-3 border-0 small mb-0">
                            <i class="bi bi-shield-check me-2"></i>
                            El usuario recibirá un correo con un enlace para establecer su contraseña.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i>
                <span class="fw-semibold d-none d-sm-inline">Crear Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('instance.users.index', $instance->id)); ?>" class="btn-cancel">Cancelar</a>
                <button type="submit" form="userForm" class="btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCards = document.querySelectorAll('.role-card');
        const summaryRole = document.getElementById('summaryRole');
        const nameInput = document.querySelector('input[name="name"]');
        const emailInput = document.querySelector('input[name="email"]');
        const summaryName = document.getElementById('summaryName');
        const summaryEmail = document.getElementById('summaryEmail');
        const passInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');

        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                roleCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;

                const roleName = this.querySelector('.role-name').textContent;
                summaryRole.className = 'badge rounded-pill px-3 py-2';
                summaryRole.style.background = 'rgba(59,130,246,.1)';
                summaryRole.style.color = '#2563eb';
                summaryRole.innerHTML = '<i class="bi bi-shield-check me-1"></i> ' + roleName;
            });
        });

        nameInput.addEventListener('input', function() {
            const val = this.value || 'Sin nombre';
            summaryName.textContent = val;
        });

        emailInput.addEventListener('input', function() {
            const val = this.value || 'sin email';
            summaryEmail.textContent = val;
        });

        if (passInput && strengthBar) {
            passInput.addEventListener('input', function() {
                const v = this.value;
                let score = 0;
                if (v.length >= 12) score += 30;
                if (v.length >= 16) score += 20;
                if (/[A-Z]/.test(v)) score += 20;
                if (/[0-9]/.test(v)) score += 20;
                if (/[^A-Za-z0-9]/.test(v)) score += 10;
                strengthBar.style.width = score + '%';
                strengthBar.style.background = score < 30 ? '#ef4444' : score < 60 ? '#f59e0b' : '#22c55e';
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/instance/users/create.blade.php ENDPATH**/ ?>