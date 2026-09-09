<?php $__env->startSection('title', 'Editar Orden #' . $orden->numero_orden); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Orden #<?php echo e($orden->numero_orden); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-tools me-1"></i>
                        Modificando orden de reparación
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('tecnicas.show', $orden)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
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
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(59,130,246,.05);border-left:4px solid #3b82f6 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#3b82f6;background:rgba(59,130,246,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando la orden:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;">#<?php echo e($orden->numero_orden); ?> - <?php echo e($orden->cliente); ?></strong>
            </div>
        </div>
    </div>

    <form id="tecnicasForm" method="POST" action="<?php echo e(route('tecnicas.update', $orden)); ?>">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información del Cliente</div>
            <div class="ui-card-subtitle">Selecciona o modifica el cliente</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $orden->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                    <?php echo e($cliente->nombre); ?> - <?php echo e($cliente->rnc_cedula); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="ui-select <?php $__errorArgs = ['tipo_servicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar...</option>
                            <option value="hardware" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'hardware' ? 'selected' : ''); ?>>Hardware</option>
                            <option value="software" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'software' ? 'selected' : ''); ?>>Software</option>
                            <option value="desbloqueo" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'desbloqueo' ? 'selected' : ''); ?>>Desbloqueo</option>
                            <option value="recuperacion_datos" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'recuperacion_datos' ? 'selected' : ''); ?>>Recuperación de Datos</option>
                            <option value="mantenimiento" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'mantenimiento' ? 'selected' : ''); ?>>Mantenimiento</option>
                            <option value="personalizacion" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'personalizacion' ? 'selected' : ''); ?>>Personalización</option>
                            <option value="otro" <?php echo e(old('tipo_servicio', $orden->tipo_servicio) == 'otro' ? 'selected' : ''); ?>>Otro</option>
                        </select>
                        <?php $__errorArgs = ['tipo_servicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-phone"></i> Información del Equipo</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Equipo</label>
                        <select name="equipo_id" class="ui-select <?php $__errorArgs = ['equipo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin equipo</option>
                            <?php $__currentLoopData = $equipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($equipo->id); ?>" <?php echo e(old('equipo_id', $orden->equipo_id) == $equipo->id ? 'selected' : ''); ?>>
                                    <?php echo e($equipo->serial_imei); ?> - <?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['equipo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin asignar</option>
                            <?php $__currentLoopData = $tecnicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tech->id); ?>" <?php echo e(old('tecnico_id', $orden->tecnico_id) == $tech->id ? 'selected' : ''); ?>>
                                    <?php echo e($tech->nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Problema Reportado <span class="text-danger">*</span></label>
                        <textarea name="problema_reportado" class="ui-input <?php $__errorArgs = ['problema_reportado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" required><?php echo e(old('problema_reportado', $orden->problema_reportado)); ?></textarea>
                        <?php $__errorArgs = ['problema_reportado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Diagnóstico</label>
                        <textarea name="diagnostico" class="ui-input <?php $__errorArgs = ['diagnostico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2"><?php echo e(old('diagnostico', $orden->diagnostico)); ?></textarea>
                        <?php $__errorArgs = ['diagnostico'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Solución Aplicada</label>
                        <textarea name="solucion_aplicada" class="ui-input <?php $__errorArgs = ['solucion_aplicada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="2"><?php echo e(old('solucion_aplicada', $orden->solucion_aplicada)); ?></textarea>
                        <?php $__errorArgs = ['solucion_aplicada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Fecha Estimada de Entrega</label>
                        <input type="date" name="fecha_entrega_estimada" class="ui-input <?php $__errorArgs = ['fecha_entrega_estimada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_entrega_estimada', $orden->fecha_entrega_estimada?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['fecha_entrega_estimada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Método de Pago</label>
                        <select name="metodo_pago" class="ui-select <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo" <?php echo e(old('metodo_pago', $orden->metodo_pago) == 'efectivo' ? 'selected' : ''); ?>>Efectivo</option>
                            <option value="transferencia" <?php echo e(old('metodo_pago', $orden->metodo_pago) == 'transferencia' ? 'selected' : ''); ?>>Transferencia</option>
                            <option value="tarjeta" <?php echo e(old('metodo_pago', $orden->metodo_pago) == 'tarjeta' ? 'selected' : ''); ?>>Tarjeta</option>
                            <option value="NCF" <?php echo e(old('metodo_pago', $orden->metodo_pago) == 'NCF' ? 'selected' : ''); ?>>NCF</option>
                        </select>
                        <?php $__errorArgs = ['metodo_pago'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.3s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-currency-dollar"></i> Costos</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label class="ui-label">Costo Piezas</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="costo_piezas" class="ui-input" step="0.01" min="0" value="<?php echo e(old('costo_piezas', $orden->costo_piezas)); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Mano de Obra</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="mano_obra" class="ui-input" step="0.01" min="0" value="<?php echo e(old('mano_obra', $orden->mano_obra)); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="<?php echo e(old('descuento', $orden->descuento)); ?>">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="ui-label">Total Calculado</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" id="total_display" class="form-control" readonly value="<?php echo e(number_format($orden->total, 2)); ?>" style="font-weight: 700;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.4s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-shield-check"></i> Garantía y Notas</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="garantia_extendida" id="garantia_extendida" value="1" <?php echo e(old('garantia_extendida', $orden->garantia_extendida) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="garantia_extendida">Garantía Extendida</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3"><?php echo e(old('notas', $orden->notas)); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#3b82f6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando orden #<?php echo e($orden->numero_orden); ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('tecnicas.show', $orden)); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="tecnicasForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    function calcularTotal() {
        const piezas = parseFloat($('input[name="costo_piezas"]').val()) || 0;
        const manoObra = parseFloat($('input[name="mano_obra"]').val()) || 0;
        const descuento = parseFloat($('input[name="descuento"]').val()) || 0;
        const subtotal = piezas + manoObra;
        const base = Math.max(subtotal - descuento, 0);
        const itbis = base * 0.18;
        const total = base + itbis;
        $('#total_display').val(total.toFixed(2));
    }

    $('input[name="costo_piezas"], input[name="mano_obra"], input[name="descuento"]').on('input', calcularTotal);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tecnicas/edit.blade.php ENDPATH**/ ?>