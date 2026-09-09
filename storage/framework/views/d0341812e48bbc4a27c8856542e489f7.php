<?php $__env->startSection('title', 'Editar Instancia'); ?>

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
                    <h3 class="fw-bold mb-1">Editar Instancia</h3>
                    <p class="mb-0 opacity-75"><?php echo e($instance->nombre); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-info-circle me-2"></i>Informaci&oacute;n de la Instancia</h5>
            <form method="POST" action="<?php echo e(route('owner.instances.update', $instance)); ?>" id="instanceForm">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input rounded-pill <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $instance->nombre)); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Slug</label>
                        <input type="text" class="ui-input rounded-pill bg-light" value="<?php echo e($instance->slug); ?>" disabled>
                        <small class="text-muted">El slug no se puede modificar.</small>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rnc', $instance->rnc)); ?>">
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
                            <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type->id); ?>" <?php echo e(old('business_type_id', $instance->business_type_id) == $type->id ? 'selected' : ''); ?>><?php echo e($type->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Plan</label>
                        <select name="plan_id" class="ui-select rounded-pill <?php $__errorArgs = ['plan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="planSelect">
                            <option value="">Personalizado</option>
                            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($plan->id); ?>" data-precio="<?php echo e($plan->precio_mensual); ?>" <?php echo e(old('plan_id', $instance->plan_id) == $plan->id ? 'selected' : ''); ?>>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('costo_mensual', $instance->costo_mensual)); ?>" step="0.01" min="0" placeholder="0.00" id="costoMensual">
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
                                <option value="<?php echo e($owner->id); ?>" <?php echo e(old('owner_user_id', $instance->owner_user_id) == $owner->id ? 'selected' : ''); ?>><?php echo e($owner->name); ?> (<?php echo e($owner->email); ?>)</option>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_vencimiento', $instance->fecha_vencimiento?->format('Y-m-d'))); ?>">
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $instance->email)); ?>">
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('telefono', $instance->telefono)); ?>">
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
unset($__errorArgs, $__bag); ?>" rows="2"><?php echo e(old('direccion', $instance->direccion)); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" <?php echo e(old('activo', $instance->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="ui-card mt-5" style="--delay:.2s;border-left: 4px solid #dc2626 !important;">
        <div class="card-header text-white py-3" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div>
                    <h5 class="fw-bold mb-0 text-white">Zona de Peligro</h5>
                    <small class="text-white text-opacity-75">Acciones destructivas que no se pueden deshacer</small>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3 mb-3">
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px;">
                    <i class="bi bi-eraser fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1">Limpiar todos los datos de la instancia</h6>
                    <p class="text-muted small mb-0">
                        Esta acci&oacute;n eliminar&aacute; <strong>todos los datos operacionales</strong> de
                        <strong><?php echo e($instance->nombre); ?></strong> y reiniciar&aacute; el wizard de configuraci&oacute;n.
                    </p>
                    <div class="mt-2">
                        <small class="text-muted d-block"><i class="bi bi-x-circle-fill text-danger me-1"></i><strong>Se eliminar&aacute;n:</strong></small>
                        <div class="row row-cols-2 row-cols-md-3 g-1 mt-1">
                            <?php $__currentLoopData = [
                                'Ventas y pagos','Detalles de ventas','Compras y detalles',
                                'Cotizaciones','Conduces','Devoluciones',
                                'Gastos','Almacenes y movimientos','Cajas y sesiones',
                                'Productos','Categorías','Clientes',
                                'Proveedores','Sucursales','NCF / ECF / Secuencias',
                                'Mesas y reservaciones','Lavadero (citas/servicios)','Listas de precio',
                                'Parámetros del sistema','Logs de errores','Datos restaurante',
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col">
                                <span class="ui-badge ui-badge-danger fw-normal px-2 py-1 rounded-pill w-100 text-start">
                                    <i class="bi bi-dash-circle me-1 opacity-75"></i><?php echo e($item); ?>

                                </span>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-3 mb-3" style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.15);">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle text-danger mt-1"></i>
                    <div class="small">
                        <strong class="text-danger">Se conservar&aacute;n:</strong> usuarios de la instancia, roles y permisos,
                        m&oacute;dulos habilitados, historial de pagos y configuraci&oacute;n general de la instancia.
                        <br class="mb-1">
                        <strong class="text-warning">⚠ El wizard de configuraci&oacute;n inicial se reiniciar&aacute;</strong> &mdash; el usuario
                        deber&aacute; completarlo nuevamente al ingresar.
                        <br class="mb-1">
                        <strong class="text-danger">No se puede deshacer.</strong> Realiza un backup antes de continuar.
                    </div>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('owner.instances.clean', $instance)); ?>" 
                  onsubmit="return UI.confirm.delete('¿ESTÁS ABSOLUTAMENTE SEGURO? Esta acci&oacute;n eliminar&aacute; TODOS los datos de <?php echo e($instance->nombre); ?>. No se puede deshacer.')">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="ui-label fw-bold text-danger">Escribe <strong><?php echo e($instance->nombre); ?></strong> para confirmar:</label>
                    <div class="ui-input-group">
                        <span class="ui-input-group-text bg-white border-end-0"><i class="bi bi-key text-danger"></i></span>
                        <input type="text" name="confirm_name" class="ui-input border-start-0" 
                               placeholder="Escribe el nombre exacto de la instancia" 
                               autocomplete="off" required
                               oninput="document.getElementById('clean-btn').disabled = (this.value !== '<?php echo e($instance->nombre); ?>')">
                    </div>
                </div>
                <button type="submit" id="clean-btn" class="ui-btn ui-btn-danger rounded-pill px-4 fw-bold" disabled>
                    <i class="bi bi-eraser me-2"></i>Limpiar Todos los Datos
                </button>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Editando: <?php echo e($instance->nombre); ?></span>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5 fw-bold shadow-sm" style="background:#8b5cf6;border-color:#8b5cf6;color:#fff;">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('planSelect')?.addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const precio = opt ? opt.dataset.precio : '';
    if (precio !== '' && precio !== undefined) {
        document.getElementById('costoMensual').value = precio;
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/edit.blade.php ENDPATH**/ ?>