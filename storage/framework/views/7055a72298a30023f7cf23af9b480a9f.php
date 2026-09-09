<?php $__env->startSection('title', 'Nueva Vivienda'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#f59e0b;--accent-rgb:245,158,11;--accent-hover:#d97706;">

    <div class="ui-header mb-4" style="--delay:0s; background: linear-gradient(135deg, #6366f1, #8b5cf6, #a855f7);">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-house-add"></i></div>
                <div><h4 class="ui-header-title">Nueva Vivienda</h4><div class="ui-header-meta">Registra una nueva propiedad para alquiler</div></div>
            </div>
        </div>
    </div>

    <form action="<?php echo e(route('alquileres.viviendas.store')); ?>" method="POST" id="instanceForm">
        <?php echo csrf_field(); ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="ui-card" style="--delay:.1s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle"></i>
                        Informaci&oacute;n General
                    </div>
                    <div class="ui-card-subtitle">Datos b&aacute;sicos de la propiedad</div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nombre" class="ui-label">Nombre de la vivienda <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-house"></i></span>
                                        <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre')); ?>" placeholder="Ej: Apartamento 101" required>
                                    </div>
                                    <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="tipo" class="ui-label">Tipo</label>
                                    <select name="tipo" id="tipo" class="ui-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                        <option value="apartamento" <?php echo e(old('tipo')=='apartamento'?'selected':''); ?>>Apartamento</option>
                                        <option value="casa" <?php echo e(old('tipo')=='casa'?'selected':''); ?>>Casa</option>
                                        <option value="local" <?php echo e(old('tipo')=='local'?'selected':''); ?>>Local</option>
                                        <option value="habitacion" <?php echo e(old('tipo')=='habitacion'?'selected':''); ?>>Habitaci&oacute;n</option>
                                        <option value="oficina" <?php echo e(old('tipo')=='oficina'?'selected':''); ?>>Oficina</option>
                                        <option value="otro" <?php echo e(old('tipo')=='otro'?'selected':''); ?>>Otro</option>
                                    </select>
                                    <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="direccion" class="ui-label">Direcci&oacute;n</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="direccion" id="direccion" class="ui-input <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('direccion')); ?>" placeholder=" ">
                                    </div>
                                    <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="descripcion" class="ui-label">Descripci&oacute;n</label>
                                    <textarea name="descripcion" id="descripcion" class="ui-textarea <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder=" " rows="3"><?php echo e(old('descripcion')); ?></textarea>
                                    <?php $__errorArgs = ['descripcion'];
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
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ui-card" style="--delay:.2s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-sliders"></i>
                        Detalles
                    </div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="habitaciones" class="ui-label">Habitaciones</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-door-open"></i></span>
                                        <input type="number" name="habitaciones" id="habitaciones" class="ui-input" value="<?php echo e(old('habitaciones', 0)); ?>" min="0" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="banos" class="ui-label">Ba&ntilde;os</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-droplet"></i></span>
                                        <input type="number" name="banos" id="banos" class="ui-input" value="<?php echo e(old('banos', 0)); ?>" min="0" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="area_m2" class="ui-label">&Aacute;rea (m&sup2;)</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-rulers"></i></span>
                                        <input type="number" step="0.01" name="area_m2" id="area_m2" class="ui-input" value="<?php echo e(old('area_m2')); ?>" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ui-card" style="--delay:.3s;">
                    <div class="ui-card-accent purple"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-cash-coin"></i>
                        Valores
                    </div>
                    <div class="ui-card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_alquiler" class="ui-label">Monto Alquiler <span class="text-danger">*</span></label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-currency-dollar"></i></span>
                                        <input type="number" step="0.01" name="monto_alquiler" id="monto_alquiler" class="ui-input <?php $__errorArgs = ['monto_alquiler'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('monto_alquiler')); ?>" placeholder=" " required>
                                    </div>
                                    <?php $__errorArgs = ['monto_alquiler'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="monto_deposito" class="ui-label">Dep&oacute;sito</label>
                                    <div class="ui-input-group">
                                        <span class="ui-input-group-text"><i class="bi bi-shield-check"></i></span>
                                        <input type="number" step="0.01" name="monto_deposito" id="monto_deposito" class="ui-input" value="<?php echo e(old('monto_deposito', 0)); ?>" placeholder=" ">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="estado" class="ui-label">Estado</label>
                                    <select name="estado" id="estado" class="ui-select" required>
                                        <option value="disponible" <?php echo e(old('estado','disponible')=='disponible'?'selected':''); ?>>Disponible</option>
                                        <option value="alquilado" <?php echo e(old('estado')=='alquilado'?'selected':''); ?>>Alquilado</option>
                                        <option value="mantenimiento" <?php echo e(old('estado')=='mantenimiento'?'selected':''); ?>>Mantenimiento</option>
                                        <option value="inactivo" <?php echo e(old('estado')=='inactivo'?'selected':''); ?>>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    <div style="height: 80px;"></div>
</div>
<?php $__env->stopSection(); ?>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="<?php echo e(route('alquileres.viviendas.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill px-4 me-2">Cancelar</a>
        <button type="submit" form="instanceForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Vivienda
        </button>
    </div>
</div>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/alquileres/viviendas/create.blade.php ENDPATH**/ ?>