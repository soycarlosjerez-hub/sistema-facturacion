<?php $__env->startSection('title', 'Editar Riesgo'); ?>

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
                    <i class="bi bi-shield-exclamation"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar <?php echo e($riesgo->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.riesgos.show', $riesgo)); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando riesgo — <?php echo e($riesgo->area); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.riesgos.update', $riesgo)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label-custom">Código</label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="<?php echo e(old('codigo', $riesgo->codigo)); ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Área</label>
                        <input type="text" name="area" class="form-control form-control-custom" value="<?php echo e(old('area', $riesgo->area)); ?>">
                        <?php $__errorArgs = ['area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Fecha Límite</label>
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="<?php echo e(old('fecha_limite', $riesgo->fecha_limite?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['fecha_limite'];
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
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3"><?php echo e(old('descripcion', $riesgo->descripcion)); ?></textarea>
                        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Causa</label>
                        <textarea name="causa" class="form-control form-control-custom" rows="2"><?php echo e(old('causa', $riesgo->causa)); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Consecuencia</label>
                        <textarea name="consecuencia" class="form-control form-control-custom" rows="2"><?php echo e(old('consecuencia', $riesgo->consecuencia)); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Probabilidad (1-5)</label>
                        <select name="probabilidad" class="form-select form-select-custom">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(old('probabilidad', $riesgo->probabilidad) == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                            <?php endfor; ?>
                        </select>
                        <?php $__errorArgs = ['probabilidad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Impacto (1-5)</label>
                        <select name="impacto" class="form-select form-select-custom">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(old('impacto', $riesgo->impacto) == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                            <?php endfor; ?>
                        </select>
                        <?php $__errorArgs = ['impacto'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Clasificación</label>
                        <select name="clasificacion" class="form-select form-select-custom">
                            <option value="bajo" <?php echo e(old('clasificacion', $riesgo->clasificacion) == 'bajo' ? 'selected' : ''); ?>>Bajo</option>
                            <option value="medio" <?php echo e(old('clasificacion', $riesgo->clasificacion) == 'medio' ? 'selected' : ''); ?>>Medio</option>
                            <option value="alto" <?php echo e(old('clasificacion', $riesgo->clasificacion) == 'alto' ? 'selected' : ''); ?>>Alto</option>
                            <option value="critico" <?php echo e(old('clasificacion', $riesgo->clasificacion) == 'critico' ? 'selected' : ''); ?>>Crítico</option>
                        </select>
                        <?php $__errorArgs = ['clasificacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Controles Existentes</label>
                        <textarea name="controles_existentes" class="form-control form-control-custom" rows="2"><?php echo e(old('controles_existentes', $riesgo->controles_existentes)); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Acción</label>
                        <textarea name="plan_accion" class="form-control form-control-custom" rows="2"><?php echo e(old('plan_accion', $riesgo->plan_accion)); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Mitigación</label>
                        <textarea name="plan_mitigacion" class="form-control form-control-custom" rows="2"><?php echo e(old('plan_mitigacion', $riesgo->plan_mitigacion)); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin responsable</option>
                            <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('responsable_id', $riesgo->responsable_id) == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
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
                            <option value="identificado" <?php echo e(old('estado', $riesgo->estado) == 'identificado' ? 'selected' : ''); ?>>Identificado</option>
                            <option value="en_tratamiento" <?php echo e(old('estado', $riesgo->estado) == 'en_tratamiento' ? 'selected' : ''); ?>>En Tratamiento</option>
                            <option value="cerrado" <?php echo e(old('estado', $riesgo->estado) == 'cerrado' ? 'selected' : ''); ?>>Cerrado</option>
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
                        <label class="form-label-custom">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-custom" rows="2"><?php echo e(old('observaciones', $riesgo->observaciones)); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="<?php echo e(route('sgc.riesgos.show', $riesgo)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/riesgos/edit.blade.php ENDPATH**/ ?>