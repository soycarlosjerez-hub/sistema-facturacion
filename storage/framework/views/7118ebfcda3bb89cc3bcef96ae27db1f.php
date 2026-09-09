<?php $__env->startSection('title', 'Editar Licencia de Software'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Licencia</h4>
                    <div class="ui-header-meta">Actualiza los datos de la licencia</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="<?php echo e(route('licencias-software.update', $licenciaSoftware)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="producto_id" class="form-label fw-bold">Producto</label>
                            <select name="producto_id" id="producto_id" class="form-select <?php $__errorArgs = ['producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Seleccionar producto --</option>
                                <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($producto->id); ?>" <?php echo e(old('producto_id', $licenciaSoftware->producto_id) == $producto->id ? 'selected' : ''); ?>>
                                    <?php echo e($producto->nombre); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['producto_id'];
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
                            <label for="clave_licencia" class="form-label fw-bold">Clave de Licencia *</label>
                            <input type="text" name="clave_licencia" id="clave_licencia" class="form-control <?php $__errorArgs = ['clave_licencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('clave_licencia', $licenciaSoftware->clave_licencia)); ?>" required>
                            <?php $__errorArgs = ['clave_licencia'];
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
                                <label for="tipo_licencia" class="form-label fw-bold">Tipo de Licencia</label>
                                <select name="tipo_licencia" id="tipo_licencia" class="form-select <?php $__errorArgs = ['tipo_licencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="perpetua" <?php echo e(old('tipo_licencia', $licenciaSoftware->tipo_licencia) == 'perpetua' ? 'selected' : ''); ?>>Perpetua</option>
                                    <option value="suscripcion" <?php echo e(old('tipo_licencia', $licenciaSoftware->tipo_licencia) == 'suscripcion' ? 'selected' : ''); ?>>Suscripción</option>
                                    <option value="open_source" <?php echo e(old('tipo_licencia', $licenciaSoftware->tipo_licencia) == 'open_source' ? 'selected' : ''); ?>>Open Source</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="plataforma" class="form-label fw-bold">Plataforma</label>
                                <select name="plataforma" id="plataforma" class="form-select <?php $__errorArgs = ['plataforma'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <option value="">-- Seleccionar --</option>
                                    <option value="Windows" <?php echo e(old('plataforma', $licenciaSoftware->plataforma) == 'Windows' ? 'selected' : ''); ?>>Windows</option>
                                    <option value="macOS" <?php echo e(old('plataforma', $licenciaSoftware->plataforma) == 'macOS' ? 'selected' : ''); ?>>macOS</option>
                                    <option value="Linux" <?php echo e(old('plataforma', $licenciaSoftware->plataforma) == 'Linux' ? 'selected' : ''); ?>>Linux</option>
                                    <option value="Cloud" <?php echo e(old('plataforma', $licenciaSoftware->plataforma) == 'Cloud' ? 'selected' : ''); ?>>Cloud/Web</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="usuario_asignado" class="form-label fw-bold">Usuario Asignado</label>
                            <input type="text" name="usuario_asignado" id="usuario_asignado" class="form-control <?php $__errorArgs = ['usuario_asignado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('usuario_asignado', $licenciaSoftware->usuario_asignado)); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label fw-bold">Fecha de Vencimiento</label>
                            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control <?php $__errorArgs = ['fecha_vencimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_vencimiento', $licenciaSoftware->fecha_vencimiento?->format('Y-m-d'))); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="notas" class="form-label fw-bold">Notas</label>
                            <textarea name="notas" id="notas" class="form-control <?php $__errorArgs = ['notas'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('notas', $licenciaSoftware->notas)); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="licencia_activa" id="licencia_activa" <?php echo e(old('licencia_activa', $licenciaSoftware->licencia_activa) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="licencia_activa">Licencia Activa</label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Licencia
                            </button>
                            <a href="<?php echo e(route('licencias-software.index')); ?>" class="btn btn-secondary">
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/licencias-software/edit.blade.php ENDPATH**/ ?>