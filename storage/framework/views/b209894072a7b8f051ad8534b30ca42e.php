<?php $__env->startSection('title', 'Nueva Configuración de Garantía'); ?>

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
                    <div class="ui-header-title">Nueva Configuración de Garantía</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-plus-circle me-1"></i>
                        Define los parámetros de garantía para productos
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('garantias-config.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ui-card" style="--delay:.1s">
                <div class="ui-card-accent"></div>
                <form id="garantiaConfigForm" action="<?php echo e(route('garantias-config.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #16a34a;">
                            <i class="bi bi-info-circle me-2"></i>Información Básica
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nombre" class="ui-label">Nombre de la Garantía <span class="text-danger">*</span></label>
                                <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre')); ?>" required>
                                <small class="text-muted">Ej: Garantía Estándar Laptops, Garantía Extendida Servidores</small>
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
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                            <i class="bi bi-box-seam me-2"></i>Configuración de Cobertura
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipo_producto" class="ui-label">Tipo de Producto</label>
                                <select name="tipo_producto" id="tipo_producto" class="ui-select <?php $__errorArgs = ['tipo_producto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- General (todos los productos) --</option>
                                    <option value="laptop" <?php echo e(old('tipo_producto') == 'laptop' ? 'selected' : ''); ?>>Laptop</option>
                                    <option value="desktop" <?php echo e(old('tipo_producto') == 'desktop' ? 'selected' : ''); ?>>Desktop/PC</option>
                                    <option value="servidor" <?php echo e(old('tipo_producto') == 'servidor' ? 'selected' : ''); ?>>Servidor</option>
                                    <option value="impresora" <?php echo e(old('tipo_producto') == 'impresora' ? 'selected' : ''); ?>>Impresora</option>
                                    <option value="red" <?php echo e(old('tipo_producto') == 'red' ? 'selected' : ''); ?>>Equipo de Red</option>
                                    <option value="camara" <?php echo e(old('tipo_producto') == 'camara' ? 'selected' : ''); ?>>Cámara/Seguridad</option>
                                    <option value="accesorio" <?php echo e(old('tipo_producto') == 'accesorio' ? 'selected' : ''); ?>>Accesorio</option>
                                    <option value="software" <?php echo e(old('tipo_producto') == 'software' ? 'selected' : ''); ?>>Software</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="dias_garantia" class="ui-label">Días de Garantía <span class="text-danger">*</span></label>
                                <input type="number" name="dias_garantia" id="dias_garantia" class="ui-input <?php $__errorArgs = ['dias_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('dias_garantia', 90)); ?>" min="0" required>
                            </div>
                        </div>
                    </div>

                    <div class="ui-card-body pb-4 mb-4 border-bottom">
                        <h6 class="fw-bold mb-3" style="color: #059669;">
                            <i class="bi bi-shield-check me-2"></i>Tipo de Garantía
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="tipo_garantia" class="ui-label">Tipo de Garantía <span class="text-danger">*</span></label>
                                <select name="tipo_garantia" id="tipo_garantia" class="ui-select <?php $__errorArgs = ['tipo_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">-- Seleccionar --</option>
                                    <option value="fabrica" <?php echo e(old('tipo_garantia') == 'fabrica' ? 'selected' : ''); ?>>Garantía de Fábrica</option>
                                    <option value="extendida" <?php echo e(old('tipo_garantia') == 'extendida' ? 'selected' : ''); ?>>Garantía Extendida</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="orden" class="ui-label">Orden de Visualización</label>
                                <input type="number" name="orden" id="orden" class="ui-input <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('orden', 0)); ?>" min="0">
                            </div>
                            <div class="col-12">
                                <label for="cobertura" class="ui-label">Cobertura</label>
                                <textarea name="cobertura" id="cobertura" class="ui-textarea <?php $__errorArgs = ['cobertura'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Describe qué cubre la garantía y qué no"><?php echo e(old('cobertura')); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label for="terminos_por_defecto" class="ui-label">
                                    <i class="bi bi-file-earmark-text me-1"></i>Terminos por Defecto
                                </label>
                                <textarea name="terminos_por_defecto" id="terminos_por_defecto" class="ui-textarea <?php $__errorArgs = ['terminos_por_defecto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Terminos de garantia predeterminados para este tipo de producto"><?php echo e(old('terminos_por_defecto')); ?></textarea>
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
                        </div>
                    </div>

                    <div class="ui-card-body">
                        <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                            <i class="bi bi-gear me-2"></i>Estado
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="activo" class="ui-label">Estado</label>
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:rgba(34,197,94,.05);">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo e(old('activo', true) ? 'checked' : ''); ?> role="switch" style="width:3em;height:1.5em;">
                                        <label class="form-check-label fw-semibold ms-2" for="activo">Configuración Activa</label>
                                    </div>
                                    <small class="text-muted">Si está inactiva no se aplicará a nuevos productos.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#22c55e;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nueva configuración de garantía</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('garantias-config.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="garantiaConfigForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Configuración
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/garantias-config/create.blade.php ENDPATH**/ ?>