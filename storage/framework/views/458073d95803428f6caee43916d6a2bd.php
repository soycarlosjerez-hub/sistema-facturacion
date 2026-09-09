<?php $__env->startSection('title', 'Registrar Pago - ' . $instance->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#10b981;--accent-rgb:16,185,129;--accent-hover:#059669">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Registrar Pago</h2>
                    <p class="mb-0 opacity-75"><?php echo e($instance->nombre); ?> &middot; Costo mensual: <?php echo e($systemMoneda ?? 'RD$'); ?> <?php echo e(number_format($instance->costo_mensual ?? 0, 2)); ?></p>
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
        <div class="ui-card-accent" style="background:#10b981"></div>
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(route('owner.instances.pagos.store', $instance)); ?>" id="instanceForm">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Mes a Pagar <span class="text-danger">*</span></label>
                    <select name="mes_pagado" class="ui-select rounded-pill <?php $__errorArgs = ['mes_pagado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="">Seleccionar mes...</option>
                        <?php $__currentLoopData = $mesesDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>" <?php echo e(old('mes_pagado') === $val ? 'selected' : ''); ?>><?php echo e(ucfirst($label)); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['mes_pagado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Monto <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-pill"><?php echo e($systemMoneda ?? 'RD$'); ?></span>
                        <input type="number" name="monto" class="ui-input rounded-end-pill <?php $__errorArgs = ['monto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('monto', $instance->costo_mensual)); ?>" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">M&eacute;todo de Pago <span class="text-danger">*</span></label>
                    <select name="metodo_pago" class="ui-select rounded-pill <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                        <option value="">Seleccionar...</option>
                        <option value="Transferencia" <?php echo e(old('metodo_pago') === 'Transferencia' ? 'selected' : ''); ?>>Transferencia</option>
                        <option value="Efectivo" <?php echo e(old('metodo_pago') === 'Efectivo' ? 'selected' : ''); ?>>Efectivo</option>
                        <option value="Cheque" <?php echo e(old('metodo_pago') === 'Cheque' ? 'selected' : ''); ?>>Cheque</option>
                        <option value="Tarjeta" <?php echo e(old('metodo_pago') === 'Tarjeta' ? 'selected' : ''); ?>>Tarjeta</option>
                        <option value="PayPal" <?php echo e(old('metodo_pago') === 'PayPal' ? 'selected' : ''); ?>>PayPal</option>
                    </select>
                    <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Notas</label>
                    <textarea name="notas" class="ui-input rounded-4 <?php $__errorArgs = ['notas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2" placeholder="Notas opcionales..."><?php echo e(old('notas')); ?></textarea>
                </div>
            </form>
        </div>
    </div>

</div>
</div>

<div class="ui-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#10b981;"></i>
            <span class="fw-semibold d-none d-sm-inline">Registrando Pago</span>
        </div>
        <div>
            <a href="<?php echo e(route('owner.instances.show', $instance)); ?>" class="ui-btn ui-btn-outline me-2">Cancelar</a>
            <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid">
                <i class="bi bi-check-lg me-2"></i>Guardar
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/instances/pagos/create.blade.php ENDPATH**/ ?>