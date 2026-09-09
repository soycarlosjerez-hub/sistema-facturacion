<?php $__env->startSection('title', 'Editar Configuración de Garantía'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#22c55e;--accent-rgb:34,197,94;--accent-hover:#16a34a;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: <?php echo e($garantiasConfig->nombre); ?></h4>
                    <div class="ui-header-meta">Actualiza los parámetros de garantía</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="<?php echo e(route('garantias-config.update', $garantiasConfig)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-bold">Nombre de la Garantía *</label>
                            <input type="text" name="nombre" id="nombre" class="form-control <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $garantiasConfig->nombre)); ?>" required>
                            <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_producto" class="form-label fw-bold">Tipo de Producto</label>
                                <select name="tipo_producto" id="tipo_producto" class="form-select <?php $__errorArgs = ['tipo_producto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- General (todos los productos) --</option>
                                    <option value="laptop" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'laptop' ? 'selected' : ''); ?>>Laptop</option>
                                    <option value="desktop" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'desktop' ? 'selected' : ''); ?>>Desktop/PC</option>
                                    <option value="servidor" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'servidor' ? 'selected' : ''); ?>>Servidor</option>
                                    <option value="impresora" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'impresora' ? 'selected' : ''); ?>>Impresora</option>
                                    <option value="red" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'red' ? 'selected' : ''); ?>>Equipo de Red</option>
                                    <option value="cámara" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'cámara' ? 'selected' : ''); ?>>Cámara/Seguridad</option>
                                    <option value="accesorio" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'accesorio' ? 'selected' : ''); ?>>Accesorio</option>
                                    <option value="software" <?php echo e(old('tipo_producto', $garantiasConfig->tipo_producto) == 'software' ? 'selected' : ''); ?>>Software</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dias_garantia" class="form-label fw-bold">Días de Garantía *</label>
                                <input type="number" name="dias_garantia" id="dias_garantia" class="form-control <?php $__errorArgs = ['dias_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('dias_garantia', $garantiasConfig->dias_garantia)); ?>" min="0" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipo_garantia" class="form-label fw-bold">Tipo de Garantía *</label>
                                <select name="tipo_garantia" id="tipo_garantia" class="form-select <?php $__errorArgs = ['tipo_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="fabrica" <?php echo e(old('tipo_garantia', $garantiasConfig->tipo_garantia) == 'fabrica' ? 'selected' : ''); ?>>Garantía de Fábrica</option>
                                    <option value="extendida" <?php echo e(old('tipo_garantia', $garantiasConfig->tipo_garantia) == 'extendida' ? 'selected' : ''); ?>>Garantía Extendida</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="orden" class="form-label fw-bold">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="form-control <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('orden', $garantiasConfig->orden)); ?>" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="cobertura" class="form-label fw-bold">Cobertura</label>
                            <textarea name="cobertura" id="cobertura" class="form-control <?php $__errorArgs = ['cobertura'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('cobertura', $garantiasConfig->cobertura)); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="terminos_por_defecto" class="form-label fw-bold">
                                <i class="bi bi-file-earmark-text me-1"></i>Terminos por Defecto
                            </label>
                            <textarea name="terminos_por_defecto" id="terminos_por_defecto" class="form-control <?php $__errorArgs = ['terminos_por_defecto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('terminos_por_defecto', $garantiasConfig->terminos_por_defecto)); ?></textarea>
                            <small class="text-muted">Se aplicaran automaticamente cuando un producto de este tipo no tenga terminos propios definidos.</small>
                            <?php $__errorArgs = ['terminos_por_defecto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo e(old('activo', $garantiasConfig->activo) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="activo">Configuración Activa</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Configuración
                            </button>
                            <a href="<?php echo e(route('garantias-config.index')); ?>" class="btn btn-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias-config/edit.blade.php ENDPATH**/ ?>