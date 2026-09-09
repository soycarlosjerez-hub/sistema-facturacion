<?php $__env->startSection('title', 'Nuevo Reclamo'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nuevo Reclamo</h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.satisfaccion.reclamos')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar un reclamo, queja o sugerencia del cliente
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.satisfaccion.reclamos.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select form-select-custom" required>
                            <option value="reclamo" <?php echo e(old('tipo')=='reclamo' ? 'selected' : ''); ?>>Reclamo</option>
                            <option value="queja" <?php echo e(old('tipo')=='queja' ? 'selected' : ''); ?>>Queja</option>
                            <option value="sugerencia" <?php echo e(old('tipo')=='sugerencia' ? 'selected' : ''); ?>>Sugerencia</option>
                            <option value="cumpliment" <?php echo e(old('tipo')=='cumpliment' ? 'selected' : ''); ?>>Cumplido</option>
                        </select>
                        <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Canal <span class="text-danger">*</span></label>
                        <select name="canal" class="form-select form-select-custom" required>
                            <option value="web" <?php echo e(old('canal')=='web' ? 'selected' : ''); ?>>Sitio Web</option>
                            <option value="telefono" <?php echo e(old('canal')=='telefono' ? 'selected' : ''); ?>>Teléfono</option>
                            <option value="presencial" <?php echo e(old('canal')=='presencial' ? 'selected' : ''); ?>>Presencial</option>
                            <option value="email" <?php echo e(old('canal')=='email' ? 'selected' : ''); ?>>Email</option>
                            <option value="redes_sociales" <?php echo e(old('canal')=='redes_sociales' ? 'selected' : ''); ?>>Redes Sociales</option>
                        </select>
                        <?php $__errorArgs = ['canal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Cliente</label>
                        <select name="cliente_id" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $clientes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cli): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cli->id); ?>" <?php echo e(old('cliente_id') == $cli->id ? 'selected' : ''); ?>><?php echo e($cli->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Asignado A</label>
                        <select name="asignado_a" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('asignado_a') == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['asignado_a'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="abierto" <?php echo e(old('estado', 'abierto')=='abierto' ? 'selected' : ''); ?>>Abierto</option>
                            <option value="en_tramite" <?php echo e(old('estado')=='en_tramite' ? 'selected' : ''); ?>>En Trámite</option>
                            <option value="resuelto" <?php echo e(old('estado')=='resuelto' ? 'selected' : ''); ?>>Resuelto</option>
                            <option value="cerrado" <?php echo e(old('estado')=='cerrado' ? 'selected' : ''); ?>>Cerrado</option>
                        </select>
                        <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="4" required placeholder="Describe detalladamente el reclamo, queja o sugerencia..."><?php echo e(old('descripcion')); ?></textarea>
                        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="<?php echo e(route('sgc.satisfaccion.reclamos')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/satisfaccion/reclamos/create.blade.php ENDPATH**/ ?>