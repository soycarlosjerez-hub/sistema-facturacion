<?php $__env->startSection('title', 'Nuevo Artista'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .premium-header {
        background: linear-gradient(135deg, #8b5cf6, #a855f7, #7c3aed, #8b5cf6);
        background-size: 300% 300%;
        box-shadow: 0 8px 32px rgba(139,92,246,.25);
    }
    .premium-header::before {
        background:
            radial-gradient(circle at 30% 40%, rgba(255,255,255,.12) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,.08) 0%, transparent 50%);
    }
    .premium-card .form-check-input:checked {
        background-color: #8b5cf6;
        border-color: #8b5cf6;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 py-3 premium-page">
    <div class="premium-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3">
                <div class="premium-avatar-circle">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Artista / Tatuador</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>Registra un nuevo artista o tatuador en el estudio
                    </small>
                </div>
            </div>
            <a href="<?php echo e(route('tattoo.artistas.index')); ?>" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <?php if(session('error')): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="premium-card" style="animation-delay:.1s;">
        <div class="card-accent purple"></div>
        <h5 class="premium-card-title"><i class="bi bi-person-badge icon-purple"></i> Información del Artista</h5>
        <div class="card-body">
            <form id="artistaForm" action="<?php echo e(route('tattoo.artistas.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" name="nombre_completo" class="form-control rounded-3" required value="<?php echo e(old('nombre_completo')); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Usuario del Sistema</label>
                        <select name="user_id" class="form-select rounded-3">
                            <option value="">— No asociado —</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(old('user_id') == $u->id ? 'selected' : ''); ?>><?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="form-select rounded-3" required>
                            <option value="empleado" <?php echo e(old('tipo') === 'empleado' ? 'selected' : ''); ?>>Empleado</option>
                            <option value="externo" <?php echo e(old('tipo') === 'externo' ? 'selected' : ''); ?>>Externo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Especialidad</label>
                        <input type="text" name="especialidad" class="form-control rounded-3" value="<?php echo e(old('especialidad')); ?>" placeholder="Ej: Realismo, Tradicional, Blackwork...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Experiencia (años)</label>
                        <input type="number" name="experiencia_anos" class="form-control rounded-3" min="0" max="99" value="<?php echo e(old('experiencia_anos', 0)); ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Comisión (%) <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <input type="number" name="comision_pct" class="form-control" min="0" max="100" step="0.01" required value="<?php echo e(old('comision_pct', 30)); ?>">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Foto (URL)</label>
                        <input type="text" name="foto_perfil" class="form-control rounded-3" value="<?php echo e(old('foto_perfil')); ?>" placeholder="https://...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control rounded-3" value="<?php echo e(old('telefono')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control rounded-3" value="<?php echo e(old('whatsapp')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Instagram</label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">@</span>
                            <input type="text" name="instagram" class="form-control" value="<?php echo e(old('instagram')); ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Biografía</label>
                        <textarea name="biografia" class="form-control rounded-3" rows="3" maxlength="1000"><?php echo e(old('biografia')); ?></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Notas</label>
                        <textarea name="notas" class="form-control rounded-3" rows="2" maxlength="500"><?php echo e(old('notas')); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" <?php echo e(old('activo', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div style="height: 80px;"></div>
</div>

<div class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#8b5cf6;"></i>
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo artista</span>
        </div>
        <div>
            <a href="<?php echo e(route('tattoo.artistas.index')); ?>" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="artistaForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Artista
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/artistas/create.blade.php ENDPATH**/ ?>