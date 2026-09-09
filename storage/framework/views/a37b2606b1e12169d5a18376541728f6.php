<?php $__env->startSection('title', 'Editar Proveedor'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-3 ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <div class="ui-header-title">Editar Proveedor</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-building me-1"></i>
                        <?php echo e($proveedore->nombre); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('proveedores.index')); ?>" class="ui-btn ui-btn-solid ui-btn-sm rounded-pill">
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

    <div class="alert alert-info rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #3b82f6 !important;background:rgba(59,130,246,0.06);">
        <i class="bi bi-info-circle me-2" style="color:#3b82f6;"></i>
        <span class="fw-semibold">Estás editando el proveedor:</span>
        <strong><?php echo e($proveedore->nombre); ?></strong>
    </div>

    <form id="proveedorForm" action="<?php echo e(route('proveedores.update', $proveedore)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">

                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-building me-2"></i>Información General
                    </h6>
                    <small class="text-muted">Datos básicos del proveedor</small>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <label class="ui-label">Nombre comercial</label>
                        <input type="text" name="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $proveedore->nombre)); ?>" placeholder="Ej. Distribuidora Corripio">
                        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Teléfono</label>
                        <input type="text" name="telefono" class="ui-input <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('telefono', $proveedore->telefono)); ?>" placeholder="(000) 000-0000">
                        <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Correo electrónico</label>
                        <input type="email" name="email" class="ui-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('email', $proveedore->email)); ?>" placeholder="correo@empresa.com">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección</label>
                        <input type="text" name="direccion" class="ui-input <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('direccion', $proveedore->direccion)); ?>" placeholder="Calle, sector, ciudad">
                        <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-card-text me-2"></i>Información Fiscal
                    </h6>
                    <small class="text-muted">Datos tributarios del proveedor</small>
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-lg-4">
                        <label class="ui-label">RNC</label>
                        <input type="text" name="rnc" class="ui-input <?php $__errorArgs = ['rnc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('rnc', $proveedore->rnc)); ?>" placeholder="000-00000-0">
                        <?php $__errorArgs = ['rnc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Tipo de Persona</label>
                        <select name="tipo_persona" class="ui-select <?php $__errorArgs = ['tipo_persona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Seleccionar</option>
                            <option value="fisica" <?php echo e(old('tipo_persona', $proveedore->tipo_persona) === 'fisica' ? 'selected' : ''); ?>>Física</option>
                            <option value="juridica" <?php echo e(old('tipo_persona', $proveedore->tipo_persona) === 'juridica' ? 'selected' : ''); ?>>Jurídica</option>
                        </select>
                        <?php $__errorArgs = ['tipo_persona'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color:#4f46e5;">
                        <i class="bi bi-gear me-2"></i>Configuración
                    </h6>
                    <small class="text-muted">Opciones del proveedor</small>
                </div>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_isr" value="1" id="ret_isr" <?php echo e(old('sujeto_retencion_isr', $proveedore->sujeto_retencion_isr) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="ret_isr">Sujeto a Ret. ISR</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="sujeto_retencion_itbis" value="1" id="ret_itbis" <?php echo e(old('sujeto_retencion_itbis', $proveedore->sujeto_retencion_itbis) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="ret_itbis">Sujeto a Ret. ITBIS</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="chk-activo" <?php echo e(old('activo', $proveedore->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="chk-activo">Proveedor Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#4f46e5;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: <?php echo e($proveedore->nombre); ?></span>
        </div>
        <div>
            <a href="<?php echo e(route('proveedores.index')); ?>" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="proveedorForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/proveedores/edit.blade.php ENDPATH**/ ?>