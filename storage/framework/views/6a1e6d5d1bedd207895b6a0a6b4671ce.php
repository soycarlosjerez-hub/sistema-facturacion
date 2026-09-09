<?php $__env->startSection('title', 'Editar Exhibición'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-pencil-square me-1"></i>EDITANDO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Editar Exhibición</h2>
                    <p class="mb-0 opacity-75"><?php echo e($exhibicion->nombre); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.exhibiciones.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="POST" action="<?php echo e(route('arte.exhibiciones.update', $exhibicion)); ?>" id="exhibicionForm">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <?php $selected = $exhibicion->obras->pluck('id')->toArray(); ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label" for="nombre">Nombre *</label>
                        <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $exhibicion->nombre)); ?>" required>
                        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="ubicacion">Ubicación</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="ui-input" value="<?php echo e(old('ubicacion', $exhibicion->ubicacion)); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_inicio">Fecha inicio *</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="ui-input <?php $__errorArgs = ['fecha_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_inicio', optional($exhibicion->fecha_inicio)->format('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['fecha_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_fin">Fecha fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="ui-input" value="<?php echo e(old('fecha_fin', optional($exhibicion->fecha_fin)->format('Y-m-d'))); ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="ui-textarea" rows="3"><?php echo e(old('descripcion', $exhibicion->descripcion)); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Obras a exhibir</label>
                        <div class="border rounded-4 p-3" style="max-height:260px;overflow-y:auto;">
                            <?php $__currentLoopData = $obrasDisponibles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="obra_ids[]" value="<?php echo e($obra->id); ?>" id="obra<?php echo e($obra->id); ?>" <?php echo e(in_array($obra->id, $selected) ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="obra<?php echo e($obra->id); ?>">
                                    <?php echo e($obra->titulo); ?> — <span class="text-muted"><?php echo e($obra->artista?->nombre ?? 'Sin artista'); ?></span>
                                </label>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" name="activa" class="form-check-input" value="1" id="activa" <?php echo e(old('activa', $exhibicion->activa) ? 'checked' : ''); ?>>
                            <label class="form-check-label small fw-semibold" for="activa">Exhibición activa</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent,#8b5cf6)"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando Exhibición: <?php echo e($exhibicion->nombre); ?></span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('arte.exhibiciones.index')); ?>" class="ui-btn ui-btn-ghost btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" form="exhibicionForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Actualizar Exhibición
            </button>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/exhibiciones/edit.blade.php ENDPATH**/ ?>