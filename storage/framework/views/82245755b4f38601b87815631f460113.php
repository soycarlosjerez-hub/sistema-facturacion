<?php $__env->startSection('title', 'Editar Usuario: ' . $usuario->name); ?>

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
    .user-avatar {
        width: 72px; height: 72px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 2rem; font-weight: 800; flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }
    #editUserForm+.row, .row.g-4.align-items-start { align-items: stretch !important; }
    .row.g-4.align-items-start>.col-lg-8, .row.g-4.align-items-start>.col-lg-4 { display: flex; flex-direction: column; }
    .row.g-4.align-items-start .card { height: 100%; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<?php
    $rolActual = $usuario->roles->pluck('name')->first() ?? old('role','vendedor');
    $nombres = explode(' ',trim($usuario->name));
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
                <div class="user-avatar" style="background:rgba(255,255,255,.2); backdrop-filter:blur(8px); border:2px solid rgba(255,255,255,.35);">
                    <?php echo e(strtoupper(substr($nombres[0],0,1))); ?><?php echo e(strtoupper(substr($nombres[1] ?? '',0,1))); ?>

                </div>
                <div>
                    <span class="badge bg-white text-dark rounded-pill">
                        <i class="bi bi-pencil"></i> EDITANDO
                    </span>
                    <h2 class="fw-bold mb-0"><?php echo e($usuario->name); ?></h2>
                    <p class="mb-0 opacity-75"><?php echo e($usuario->email); ?></p>
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

    <form id="editUserForm" action="<?php echo e(route('usuarios.update',$usuario->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="business_type_id" value="<?php echo e($usuario->business_type_id ?? ''); ?>">

        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div class="ui-card h-100" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="premium-card-title"><i class="bi bi-person-vcard icon-amber"></i> Información Usuario</div>
                    <div class="card-body p-4">
                        <?php echo $__env->make('usuarios._form_fields', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <div class="role-picker mt-4">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $cfg = $rolConfig[$rol->name] ?? $defaultConfig;
                                    $cfg['label'] = $cfg['label'] ?? ucfirst(str_replace(['-', '_'], ' ', $rol->name));
                                    $cfg['desc'] = $cfg['desc'] ?? 'Rol personalizado.';
                                    $cfg['color'] = $cfg['color'] ?? '#64748b';
                                    $cfg['gradient'] = $cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)';
                                    $cfg['icon'] = $cfg['icon'] ?? 'bi-person';
                                ?>
                                <label class="role-card" style="--role-color:<?php echo e($cfg['color']); ?>;--role-gradient:<?php echo e($cfg['gradient']); ?>;">
                                    <input type="radio" name="role" value="<?php echo e($rol->name); ?>" class="d-none" <?php echo e($rolActual==$rol->name?'checked':''); ?> required>
                                    <div class="role-icon" style="background:<?php echo e($cfg['gradient']); ?>"><i class="bi <?php echo e($cfg['icon']); ?>"></i></div>
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
                        <div id="businessTypeSection" class="mt-4" style="display:none;">
                            <label class="ui-label">Tipo de Negocio</label>
                            <select name="business_type_id" id="business_type_id" class="ui-select">
                                <option value="">Seleccione</option>
                                <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->id); ?>" <?php echo e(old('business_type_id',$usuario->business_type_id)==$type->id?'selected':''); ?>><?php echo e($type->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="alert alert-info mt-3" id="businessTypeInfo" style="display:none">
                            <i class="bi bi-info-circle"></i> Administrador business solo puede gestionar este negocio.
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="ui-card sticky-top" style="--delay:.2s; top: 100px; z-index: 100;">
                    <div class="ui-card-accent"></div>
                    <div class="card-body text-center">
                        <div class="user-avatar mx-auto mb-3" style="background:<?php echo e($cfg['gradient'] ?? 'linear-gradient(135deg,#64748b,#475569)'); ?>; width:72px; height:72px; border-radius:50; font-size:2rem;">
                            <?php echo e(strtoupper(substr($usuario->name,0,1))); ?>

                        </div>
                        <h5 class="fw-bold"><?php echo e($usuario->name); ?></h5>
                        <p class="text-muted"><?php echo e($usuario->email); ?></p>
                        <?php if($usuario->roles->count()): ?>
                            <?php
                                $cfg = $rolConfig[$rolActual] ?? $defaultConfig;
                            ?>
                            <span class="badge rounded-pill" style="background:<?php echo e($cfg['color']); ?>"><?php echo e($cfg['label']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="premium-sticky-bar">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2" id="saveBarLeft">
                <i class="bi bi-info-circle text-primary"></i>
                <span class="fw-semibold d-none d-sm-inline">Editar Usuario</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('usuarios.index')); ?>" class="btn-cancel">Cancelar</a>
                <button type="submit" form="editUserForm" class="btn-save"><i class="bi bi-save me-2"></i> Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCards = document.querySelectorAll('.role-card');
        const businessTypeSection = document.getElementById('businessTypeSection');
        const businessTypeInfo = document.getElementById('businessTypeInfo');
        const businessTypeSelect = document.getElementById('business_type_id');
        const businessTypeHidden = document.querySelector('input[name="business_type_id"][type="hidden"]');
        const roleInputs = document.querySelectorAll('input[name="role"]');

        roleInputs.forEach(input => {
            if (input.checked) {
                input.closest('.role-card').classList.add('active');
            }
        });

        roleCards.forEach(card => {
            card.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    roleCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    const selectedRole = radio.value;
                    if (selectedRole === 'admin-business') {
                        businessTypeSection.style.display = 'block';
                        businessTypeInfo.style.display = 'block';
                        businessTypeSelect.required = true;
                        if (businessTypeHidden) businessTypeHidden.disabled = true;
                    } else {
                        businessTypeSection.style.display = 'none';
                        businessTypeInfo.style.display = 'none';
                        businessTypeSelect.required = false;
                        businessTypeSelect.value = '';
                        if (businessTypeHidden) businessTypeHidden.disabled = false;
                    }
                }
            });
        });

        roleInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.checked) {
                    roleCards.forEach(c => c.classList.remove('active'));
                    this.closest('.role-card').classList.add('active');
                    const selectedRole = this.value;
                    if (selectedRole === 'admin-business') {
                        businessTypeSection.style.display = 'block';
                        businessTypeInfo.style.display = 'block';
                        businessTypeSelect.required = true;
                        if (businessTypeHidden) businessTypeHidden.disabled = true;
                    } else {
                        businessTypeSection.style.display = 'none';
                        businessTypeInfo.style.display = 'none';
                        businessTypeSelect.required = false;
                        businessTypeSelect.value = '';
                        if (businessTypeHidden) businessTypeHidden.disabled = false;
                    }
                }
            });
        });

        if (businessTypeSelect) {
            businessTypeSelect.addEventListener('change', function() {
                if (businessTypeHidden) {
                    businessTypeHidden.value = this.value;
                }
            });
        }

        const initialRole = document.querySelector('input[name="role"]:checked');
        if (initialRole && initialRole.value === 'admin-business') {
            businessTypeSection.style.display = 'block';
            businessTypeInfo.style.display = 'block';
            businessTypeSelect.required = true;
            if (businessTypeHidden) businessTypeHidden.disabled = true;
        } else if (businessTypeHidden) {
            businessTypeHidden.disabled = false;
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/usuarios/edit.blade.php ENDPATH**/ ?>