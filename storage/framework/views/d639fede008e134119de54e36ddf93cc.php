<?php $__env->startSection('title', 'Editar ' . $tipo->nombre); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }
body.dark-mode .ui-page { --accent: #22d3ee; --accent-rgb: 34,211,238; --accent-hover: #06b6d4; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: <?php echo e($tipo->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-wind me-1"></i>Climatización
                        <span class="mx-2">·</span>
                        <a href="<?php echo e(route('climatizacion.tipos-equipos.index')); ?>" class="text-white-50 text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i>Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('climatizacion.tipos-equipos.update', $tipo)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0;margin-bottom:1.25rem;">
                    <i class="bi bi-info-circle"></i> Información del Tipo de Equipo
                </h5>
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="ui-label" for="nombre">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('nombre', $tipo->nombre)); ?>" placeholder="Nombre del tipo de equipo" required>
                        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label class="ui-label" for="slug">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="slug"
                               class="ui-input <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('slug', $tipo->slug)); ?>" placeholder="ej: minisplit-inverter" required>
                        <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-4">
                        <label class="ui-label" for="categoria">Categoría <span class="text-danger">*</span></label>
                        <select name="categoria" id="categoria"
                                class="ui-select <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar categoría...</option>
                            <option value="residencial" <?php echo e(old('categoria', $tipo->categoria) === 'residencial' ? 'selected' : ''); ?>>Residencial</option>
                            <option value="comercial" <?php echo e(old('categoria', $tipo->categoria) === 'comercial' ? 'selected' : ''); ?>>Comercial</option>
                            <option value="industrial" <?php echo e(old('categoria', $tipo->categoria) === 'industrial' ? 'selected' : ''); ?>>Industrial</option>
                        </select>
                        <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-4">
                        <label class="ui-label" for="icono">Icono (clase Bootstrap)</label>
                        <input type="text" name="icono" id="icono"
                               class="ui-input <?php $__errorArgs = ['icono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('icono', $tipo->icono)); ?>" placeholder="ej: bi-snow">
                        <?php $__errorArgs = ['icono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div class="form-text">Clase de Bootstrap Icons, ej: <code>bi-snow</code>, <code>bi-thermometer</code></div>
                    </div>

                    
                    <div class="col-md-2">
                        <label class="ui-label" for="orden">Orden</label>
                        <input type="number" name="orden" id="orden"
                               class="ui-input <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('orden', $tipo->orden)); ?>" min="0" placeholder="0">
                        <?php $__errorArgs = ['orden'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-2 d-flex align-items-end pb-2">
                        <div class="form-check form-switch">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" id="activo" class="form-check-input"
                                   value="1" <?php echo e(old('activo', $tipo->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="activo">
                                <i class="bi bi-toggle-on me-1"></i> Activo
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('climatizacion.tipos-equipos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid rounded-pill">
                    <i class="bi bi-check-lg"></i> Actualizar
                </button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/tipos-equipos/edit.blade.php ENDPATH**/ ?>