<?php $__env->startSection('title', 'Editar Zona de Cobertura'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#0ea5e9;--accent-rgb:14,165,233;--accent-hover:#0284c7;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-map"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Zona de Cobertura</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span><?php echo e($zone->nombre); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('delivery-zones.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><i class="bi bi-exclamation-circle me-1"></i><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="zoneForm" method="POST" action="<?php echo e(route('delivery-zones.update', $zone)); ?>">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="ui-card-body">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-info-circle me-2"></i>Información General
                    </h6>
                </div>

                <div class="mb-3">
                    <label for="nombre" class="ui-label">Nombre de la Zona <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           value="<?php echo e(old('nombre', $zone->nombre)); ?>" required maxlength="100" placeholder="Ej: Zona Norte">
                    <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="ui-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" class="ui-textarea <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              rows="2" maxlength="300" placeholder="Describe los límites o características de la zona..."><?php echo e(old('descripcion', $zone->descripcion)); ?></textarea>
                    <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-rulers me-2"></i>Parámetros de Entrega
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="radio_km" class="ui-label">Radio (km) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <input type="number" step="0.1" min="0.1" name="radio_km" id="radio_km" class="ui-input <?php $__errorArgs = ['radio_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('radio_km', $zone->radio_km)); ?>" required placeholder="5">
                            <span class="ui-input-group-text">km</span>
                        </div>
                        <?php $__errorArgs = ['radio_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="tiempo_estimado_minutos" class="ui-label">Tiempo Estimado (min) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <input type="number" min="1" name="tiempo_estimado_minutos" id="tiempo_estimado_minutos" class="ui-input <?php $__errorArgs = ['tiempo_estimado_minutos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('tiempo_estimado_minutos', $zone->tiempo_estimado_minutos)); ?>" required placeholder="30">
                            <span class="ui-input-group-text">min</span>
                        </div>
                        <?php $__errorArgs = ['tiempo_estimado_minutos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label for="minimo_para_envio_gratis" class="ui-label">Envío Gratis (RD$)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="minimo_para_envio_gratis" id="minimo_para_envio_gratis" class="ui-input <?php $__errorArgs = ['minimo_para_envio_gratis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('minimo_para_envio_gratis', $zone->minimo_para_envio_gratis ?? 0)); ?>" placeholder="0">
                        </div>
                        <?php $__errorArgs = ['minimo_para_envio_gratis'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Monto mínimo para envío gratuito en esta zona</div>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom mt-4">
                    <h6 class="fw-bold mb-0" style="color:#0ea5e9;">
                        <i class="bi bi-cash-coin me-2"></i>Tarifas
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tarifa_base" class="ui-label">Tarifa Base (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="tarifa_base" id="tarifa_base" class="ui-input <?php $__errorArgs = ['tarifa_base'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('tarifa_base', $zone->tarifa_base)); ?>" required placeholder="150.00">
                        </div>
                        <?php $__errorArgs = ['tarifa_base'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label for="tarifa_por_km" class="ui-label">Tarifa por Km (RD$) <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">$</span>
                            <input type="number" step="0.01" min="0" name="tarifa_por_km" id="tarifa_por_km" class="ui-input <?php $__errorArgs = ['tarifa_por_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('tarifa_por_km', $zone->tarifa_por_km)); ?>" required placeholder="25.00">
                        </div>
                        <?php $__errorArgs = ['tarifa_por_km'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="form-check form-switch mt-4">
                    <input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" <?php echo e(old('activo', $zone->activo) ? 'checked' : ''); ?>>
                    <label for="activo" class="form-check-label small fw-semibold">Zona Activa</label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="<?php echo e(route('delivery-zones.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">Cancelar</a>
        <button type="submit" form="zoneForm" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
            <i class="bi bi-save me-2"></i>Guardar Cambios
        </button>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/delivery-zones/edit.blade.php ENDPATH**/ ?>