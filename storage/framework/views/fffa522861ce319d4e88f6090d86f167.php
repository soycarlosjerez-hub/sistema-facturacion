<?php $__env->startSection('title', 'Editar Artista'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <a href="<?php echo e(route('tattoo.artistas.index')); ?>" class="btn btn-sm btn-outline-secondary rounded-pill mb-3">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-transparent pt-4 px-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Editar: <?php echo e($artista->nombre_completo); ?></h4>
        </div>
        <div class="card-body p-4">
            <form action="<?php echo e(route('tattoo.artistas.update', $artista)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control rounded-3" required value="<?php echo e(old('nombre_completo', $artista->nombre_completo)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Usuario del Sistema</label>
                        <select name="user_id" class="form-select rounded-3">
                            <option value="">— No asociado —</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(old('user_id', $artista->user_id) == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select rounded-3" required>
                            <option value="empleado" <?php echo e(old('tipo', $artista->tipo) === 'empleado' ? 'selected' : ''); ?>>Empleado</option>
                            <option value="externo" <?php echo e(old('tipo', $artista->tipo) === 'externo' ? 'selected' : ''); ?>>Externo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control rounded-3" value="<?php echo e(old('especialidad', $artista->especialidad)); ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Experiencia (años)</label>
                        <input type="number" name="experiencia_anos" class="form-control rounded-3" min="0" max="99" value="<?php echo e(old('experiencia_anos', $artista->experiencia_anos)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Comisión (%) <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <input type="number" name="comision_pct" class="form-control" min="0" max="100" step="0.01" required value="<?php echo e(old('comision_pct', $artista->comision_pct)); ?>">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Foto (URL)</label>
                        <input type="text" name="foto_perfil" class="form-control rounded-3" value="<?php echo e(old('foto_perfil', $artista->foto_perfil)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3" value="<?php echo e(old('telefono', $artista->telefono)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control rounded-3" value="<?php echo e(old('whatsapp', $artista->whatsapp)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Instagram</label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">@</span>
                            <input type="text" name="instagram" class="form-control" value="<?php echo e(old('instagram', $artista->instagram)); ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Biografía</label>
                        <textarea name="biografia" class="form-control rounded-3" rows="3" maxlength="1000"><?php echo e(old('biografia', $artista->biografia)); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2" maxlength="500"><?php echo e(old('notas', $artista->notas)); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" <?php echo e(old('activo', $artista->activo) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Actualizar Artista</button>
                    <a href="<?php echo e(route('tattoo.artistas.index')); ?>" class="btn btn-light rounded-pill px-4">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/artistas/edit.blade.php ENDPATH**/ ?>