<?php $__env->startSection('title', 'Editar Objetivo de Calidad'); ?>

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
                    <i class="bi bi-bullseye"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar <?php echo e($objetivo->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.objetivos.show', $objetivo)); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando objetivo de calidad
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.objetivos.update', $objetivo)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Código</label>
                        <input type="text" class="form-control form-control-custom" value="<?php echo e($objetivo->codigo); ?>" readonly>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label-custom">Título</label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="<?php echo e(old('titulo', $objetivo->titulo)); ?>">
                        <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="2"><?php echo e(old('descripcion', $objetivo->descripcion)); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Indicador</label>
                        <input type="text" name="indicador" class="form-control form-control-custom" value="<?php echo e(old('indicador', $objetivo->indicador)); ?>">
                        <?php $__errorArgs = ['indicador'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Meta</label>
                        <input type="number" name="meta" class="form-control form-control-custom" value="<?php echo e(old('meta', $objetivo->meta)); ?>" step="0.01">
                        <?php $__errorArgs = ['meta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Unidad</label>
                        <input type="text" name="unidad" class="form-control form-control-custom" value="<?php echo e(old('unidad', $objetivo->unidad)); ?>">
                        <?php $__errorArgs = ['unidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Inicio</label>
                        <input type="date" name="periodo_inicio" class="form-control form-control-custom" value="<?php echo e(old('periodo_inicio', $objetivo->periodo_inicio?->format('Y-m-d'))); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Periodo Fin</label>
                        <input type="date" name="periodo_fin" class="form-control form-control-custom" value="<?php echo e(old('periodo_fin', $objetivo->periodo_fin?->format('Y-m-d'))); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin asignar</option>
                            <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('responsable_id', $objetivo->responsable_id) == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['responsable_id'];
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
                            <option value="en_curso" <?php echo e(old('estado', $objetivo->estado) == 'en_curso' ? 'selected' : ''); ?>>En Curso</option>
                            <option value="cumplido" <?php echo e(old('estado', $objetivo->estado) == 'cumplido' ? 'selected' : ''); ?>>Cumplido</option>
                            <option value="no_cumplido" <?php echo e(old('estado', $objetivo->estado) == 'no_cumplido' ? 'selected' : ''); ?>>No Cumplido</option>
                            <option value="atrasado" <?php echo e(old('estado', $objetivo->estado) == 'atrasado' ? 'selected' : ''); ?>>Atrasado</option>
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
                    <div class="col-md-6">
                        <label class="form-label-custom">Evidencias</label>
                        <textarea name="evidencias" class="form-control form-control-custom" rows="2"><?php echo e(old('evidencias', $objetivo->evidencias)); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Acciones de Mejora</label>
                        <textarea name="acciones_mejora" class="form-control form-control-custom" rows="2"><?php echo e(old('acciones_mejora', $objetivo->acciones_mejora)); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="<?php echo e(route('sgc.objetivos.show', $objetivo)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/objetivos/edit.blade.php ENDPATH**/ ?>