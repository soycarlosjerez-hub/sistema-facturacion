<?php $__env->startSection('title', 'Nueva Instancia'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">
    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-1">Nueva Instancia</h3>
                    <p class="mb-0 opacity-75">Crear una nueva instancia de negocio multi-tenant</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.instances.index')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2"></i>Informaci&oacute;n de la Instancia</h5>
            <form method="POST" action="<?php echo e(route('owner.instances.store')); ?>" id="instanceForm">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre de la Instancia <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input rounded-pill <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre')); ?>" required placeholder="Ej: Restaurante La Esquina" id="nombreInput">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Slug <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <input type="text" name="slug" class="ui-input rounded-pill <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('slug')); ?>" required placeholder="restaurante-la-esquina" id="slugInput">
                        </div>
                        <small class="text-muted">Identificador &uacute;nico para la instancia.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">RNC</label>
                        <input type="text" name="rnc" class="ui-input rounded-pill <?php $__errorArgs = ['rnc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rnc')); ?>" placeholder="RNC">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Tipo de Negocio <span class="text-danger">*</span></label>
                        <select name="business_type_id" class="ui-select rounded-pill <?php $__errorArgs = ['business_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('business_type_id') == $type->id ? 'selected' : ''); ?>><?php echo e($type->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Plan <span class="text-danger">*</span></label>
                        <select name="plan_id" class="ui-select rounded-pill <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="planSelect" required>
                            <option value="">Seleccionar plan...</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" data-precio="<?php echo e($plan->precio_mensual); ?>" <?php echo e(old('plan_id') == $plan->id ? 'selected' : ''); ?>>
                                    <?php echo e($plan->nombre); ?> — RD$<?php echo e(number_format($plan->precio_mensual, 2)); ?>/mes
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Costo Mensual</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text bg-light border-0 rounded-start-pill">RD$</span>
                            <input type="number" name="costo_mensual" class="ui-input rounded-end-pill <?php $__errorArgs = ['costo_mensual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('costo_mensual')); ?>" step="0.01" min="0" placeholder="0.00" id="costoMensual">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Due&ntilde;o / Responsable</label>
                        <select name="owner_user_id" class="ui-select rounded-pill <?php $__errorArgs = ['owner_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $owners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($owner->id); ?>" <?php echo e(old('owner_user_id') == $owner->id ? 'selected' : ''); ?>><?php echo e($owner->name); ?> (<?php echo e($owner->email); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input rounded-pill <?php $__errorArgs = ['fecha_vencimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_vencimiento')); ?>">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Email</label>
                        <input type="email" name="email" class="ui-input rounded-pill <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email')); ?>" placeholder="Email de contacto">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Tel&eacute;fono</label>
                        <input type="text" name="telefono" class="ui-input rounded-pill <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('telefono')); ?>" placeholder="Tel&eacute;fono">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Direcci&oacute;n</label>
                        <textarea name="direccion" class="ui-input rounded-4 <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2" placeholder="Direcci&oacute;n"><?php echo e(old('direccion')); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" <?php echo e(old('activo', '1') ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="activo">Activa</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="form-check form-switch mb-0">
                        <input type="checkbox" name="crear_usuario" class="form-check-input" value="1" id="crearUsuario" <?php echo e(old('crear_usuario') ? 'checked' : ''); ?>>
                        <label class="form-check-label fw-bold" for="crearUsuario">
                            <i class="bi bi-person-plus text-primary me-1"></i>Crear usuario administrador para esta instancia
                        </label>
                    </div>
                </div>
                <div id="usuarioFields" class="row g-3 p-3 bg-light rounded-4 mb-3 <?php echo e(old('crear_usuario') ? '' : 'd-none'); ?>">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="user_name" class="ui-input rounded-pill <?php $__errorArgs = ['user_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('user_name')); ?>" placeholder="Nombre del administrador">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="user_email" class="ui-input rounded-pill <?php $__errorArgs = ['user_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('user_email')); ?>" placeholder="admin@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Contrase&ntilde;a <span class="text-danger">*</span></label>
                        <input type="password" name="user_password" class="ui-input rounded-pill <?php $__errorArgs = ['user_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Contrase&ntilde;a">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Confirmar Contrase&ntilde;a <span class="text-danger">*</span></label>
                        <input type="password" name="user_password_confirmation" class="ui-input rounded-pill" placeholder="Confirmar contrase&ntilde;a">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Rol <span class="text-danger">*</span></label>
                        <select name="user_role" class="ui-select rounded-pill <?php $__errorArgs = ['user_role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = ['gerente', 'admin', 'vendedor', 'almacen', 'contador']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role); ?>" <?php echo e(old('user_role') === $role ? 'selected' : ''); ?>><?php echo e(ucfirst($role)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <small class="text-muted">El usuario ser&aacute; asignado a esta instancia con este rol.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Creando nueva instancia</span>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:#8b5cf6;border-color:#8b5cf6;color:#fff;">
            <i class="bi bi-save me-2"></i>Crear Instancia
        </button>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('nombreInput')?.addEventListener('input', function() {
    const slug = this.value.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '');
    document.getElementById('slugInput').value = slug;
});

document.getElementById('planSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const precio = opt ? opt.dataset.precio : '';
    if (precio !== '' && precio !== undefined) {
        document.getElementById('costoMensual').value = precio;
    }
});

document.getElementById('crearUsuario')?.addEventListener('change', function() {
    document.getElementById('usuarioFields').classList.toggle('d-none', !this.checked);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/create.blade.php ENDPATH**/ ?>