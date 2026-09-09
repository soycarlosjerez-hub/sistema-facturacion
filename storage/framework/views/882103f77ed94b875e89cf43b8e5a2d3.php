<?php $__env->startSection('title', 'Nueva Caja'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-register"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Caja</h4>
                    <div class="ui-header-meta">Registra una nueva caja registradora en el sistema</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('cajas.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Volver
                </a>
            </div>
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
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('cajas.store')); ?>" method="POST" id="instanceForm">
                <?php echo csrf_field(); ?>
                <div class="ui-card">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Información de la Caja
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="ui-label fw-semibold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Ej. Caja 1, Caja Express, Mostrador 2" value="<?php echo e(old('nombre')); ?>">
                                <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-5">
                                <label class="ui-label fw-semibold">Código</label>
                                <input type="text" name="codigo" class="ui-input <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="C01, C02..." value="<?php echo e(old('codigo', $nextCode)); ?>">
                                <small class="text-muted">Identificador corto único.</small>
                                <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-12">
                                <label class="ui-label fw-semibold">Ubicación</label>
                                <input type="text" name="ubicacion" class="ui-input" placeholder="Ej. Mostrador principal, Segundo piso" value="<?php echo e(old('ubicacion')); ?>">
                            </div>

                            <?php if(isset($sucursales) && $sucursales->count()): ?>
                            <div class="col-12">
                                <label class="ui-label fw-semibold">Sucursal</label>
                                <select name="sucursal_id" class="ui-select">
                                    <option value="">Sin asignar</option>
                                    <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php echo e(old('sucursal_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->nombre); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="col-12">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);">
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" <?php echo e(old('activo', true) ? 'checked' : ''); ?>>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold mb-0" for="activo">Caja activa</label>
                                        <small class="d-block text-muted">Las cajas inactivas no pueden abrir turnos. Puedes cambiar esto después.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.2);">
                                    <label class="fw-bold mb-3 d-block">
                                        <i class="bi bi-receipt-cutoff me-1"></i>
                                        Tipos de Comprobante Permitidos
                                    </label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="sin" id="tipo_sin" <?php echo e(is_array(old('allowed_comprobante_types', ['sin','ncf','ecf'])) && in_array('sin', old('allowed_comprobante_types', ['sin','ncf','ecf'])) ? 'checked' : (old('allowed_comprobante_types') === '' ? '' : 'checked')); ?>>
                                            <span class="small fw-semibold">Sin Comprobante</span>
                                            <small class="text-muted">(B00)</small>
                                        </label>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="ncf" id="tipo_ncf" <?php echo e(is_array(old('allowed_comprobante_types', ['sin','ncf','ecf'])) && in_array('ncf', old('allowed_comprobante_types', ['sin','ncf','ecf'])) ? 'checked' : (old('allowed_comprobante_types') === '' ? '' : 'checked')); ?>>
                                            <span class="small fw-semibold">NCF</span>
                                            <small class="text-muted">(Tradicional)</small>
                                        </label>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="ecf" id="tipo_ecf" <?php echo e(is_array(old('allowed_comprobante_types', ['sin','ncf','ecf'])) && in_array('ecf', old('allowed_comprobante_types', ['sin','ncf','ecf'])) ? 'checked' : (old('allowed_comprobante_types') === '' ? '' : 'checked')); ?>>
                                            <span class="small fw-semibold">e-CF</span>
                                            <small class="text-muted">(DGII)</small>
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Selecciona los tipos de comprobante que estarán disponibles en la terminal de venta de esta caja. Por defecto, los 3 tipos están habilitados.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent);"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva caja</span>
        </div>
        <div>
            <a href="<?php echo e(route('cajas.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
                <i class="bi bi-check-lg me-2"></i>Guardar Caja
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/cajas/create.blade.php ENDPATH**/ ?>