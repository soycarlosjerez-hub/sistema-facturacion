<?php $__env->startSection('title', 'Nuevo Riesgo'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
    }
    .risk-matrix { display: grid; grid-template-columns: repeat(5,1fr); gap: 4px; max-width: 300px; }
    .risk-cell { padding: .5rem; text-align: center; border-radius: .5rem; font-size: .75rem; font-weight: 600; }
    .risk-low { background: #dcfce7; color: #16a34a; }
    .risk-med { background: #fef3c7; color: #d97706; }
    .risk-high { background: #fed7aa; color: #ea580c; }
    .risk-crit { background: #fee2e2; color: #dc2626; }
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
                    <h4 class="ui-header-title">Nuevo Riesgo</h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.riesgos.index')); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Registrar un nuevo riesgo en el SGC
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.riesgos.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-info-circle me-1"></i> Identificación del Riesgo
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Código <span class="text-danger">*</span></label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="<?php echo e(old('codigo')); ?>" placeholder="AUTO si se deja vacío">
                        <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Área <span class="text-danger">*</span></label>
                        <input type="text" name="area" class="form-control form-control-custom" value="<?php echo e(old('area')); ?>" required>
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
                        <input type="date" name="fecha_limite" class="form-control form-control-custom" value="<?php echo e(old('fecha_limite')); ?>">
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
                        <label class="form-label-custom">Descripción del Riesgo <span class="text-danger">*</span></label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3" required><?php echo e(old('descripcion')); ?></textarea>
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
                        <textarea name="causa" class="form-control form-control-custom" rows="2"><?php echo e(old('causa')); ?></textarea>
                        <?php $__errorArgs = ['causa'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Consecuencia</label>
                        <textarea name="consecuencia" class="form-control form-control-custom" rows="2"><?php echo e(old('consecuencia')); ?></textarea>
                        <?php $__errorArgs = ['consecuencia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-calculator me-1"></i> Evaluación del Riesgo
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-custom">Probabilidad (1-5) <span class="text-danger">*</span></label>
                        <select name="probabilidad" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(old('probabilidad') == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
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
                        <label class="form-label-custom">Impacto (1-5) <span class="text-danger">*</span></label>
                        <select name="impacto" class="form-select form-select-custom" required>
                            <option value="">Seleccionar...</option>
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e(old('impacto') == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
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
                            <option value="bajo" <?php echo e(old('clasificacion')=='bajo' ? 'selected' : ''); ?>>Bajo</option>
                            <option value="medio" <?php echo e(old('clasificacion', 'medio')=='medio' ? 'selected' : ''); ?>>Medio</option>
                            <option value="alto" <?php echo e(old('clasificacion')=='alto' ? 'selected' : ''); ?>>Alto</option>
                            <option value="critico" <?php echo e(old('clasificacion')=='critico' ? 'selected' : ''); ?>>Crítico</option>
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

                    
                    <div class="col-12 mt-4">
                        <h6 class="fw-bold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px;">
                            <i class="bi bi-gear me-1"></i> Tratamiento
                        </h6>
                        <hr class="mt-1 mb-3">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Controles Existentes</label>
                        <textarea name="controles_existentes" class="form-control form-control-custom" rows="2"><?php echo e(old('controles_existentes')); ?></textarea>
                        <?php $__errorArgs = ['controles_existentes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Plan de Acción</label>
                        <textarea name="plan_accion" class="form-control form-control-custom" rows="2"><?php echo e(old('plan_accion')); ?></textarea>
                        <?php $__errorArgs = ['plan_accion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-custom">Responsable</label>
                        <select name="responsable_id" class="form-select form-select-custom">
                            <option value="">Sin responsable</option>
                            <?php $__currentLoopData = $usuarios ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(old('responsable_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->name); ?></option>
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
                            <option value="identificado" <?php echo e(old('estado', 'identificado')=='identificado' ? 'selected' : ''); ?>>Identificado</option>
                            <option value="en_tratamiento" <?php echo e(old('estado')=='en_tratamiento' ? 'selected' : ''); ?>>En Tratamiento</option>
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
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="<?php echo e(route('sgc.riesgos.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/riesgos/create.blade.php ENDPATH**/ ?>