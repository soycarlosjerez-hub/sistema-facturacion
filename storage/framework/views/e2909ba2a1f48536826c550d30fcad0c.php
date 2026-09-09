<?php $__env->startSection('title', 'Nuevo Diseño'); ?>

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
                    <i class="bi bi-brush"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white">Nuevo Diseño</h4>
                    <small class="text-white opacity-75">
                        <i class="bi bi-plus-circle me-1"></i>Registra un nuevo diseño de tatuaje en el catálogo
                    </small>
                </div>
            </div>
            <a href="<?php echo e(route('tattoo.disenos.index')); ?>" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold" style="backdrop-filter:blur(8px);background:rgba(255,255,255,.2);border:1.5px solid rgba(255,255,255,.35);">
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
        <h5 class="premium-card-title"><i class="bi bi-brush icon-purple"></i> Información del Diseño</h5>
        <div class="card-body">
            <form id="disenoForm" action="<?php echo e(route('tattoo.disenos.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control rounded-3" required value="<?php echo e(old('titulo')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Estilo</label>
                        <input type="text" name="estilo" class="form-control rounded-3" value="<?php echo e(old('estilo')); ?>" placeholder="Ej: Realismo, Blackwork...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Artista (opcional)</label>
                        <select name="artist_id" class="form-select rounded-3">
                            <option value="">— Sin artista —</option>
                            <?php $__currentLoopData = $artistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($a->id); ?>" <?php echo e(old('artist_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre_completo); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Duración estimada (min) <span class="text-danger">*</span></label>
                        <input type="number" name="duracion_estimada_min" class="form-control rounded-3" min="15" value="<?php echo e(old('duracion_estimada_min', 60)); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">URL Imagen Portada</label>
                        <input type="text" name="imagen_portada" class="form-control rounded-3" value="<?php echo e(old('imagen_portada')); ?>" placeholder="https://...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Precio Mínimo <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="precio_minimo" class="form-control" min="0" step="0.01" required value="<?php echo e(old('precio_minimo', 0)); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Precio Máximo <span class="text-danger">*</span></label>
                        <div class="input-group rounded-3">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="precio_maximo" class="form-control" min="0" step="0.01" required value="<?php echo e(old('precio_maximo', 0)); ?>">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control rounded-3" rows="3" maxlength="2000"><?php echo e(old('descripcion')); ?></textarea>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="popular" class="form-check-input" id="popular" value="1" <?php echo e(old('popular') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="popular">Marcar como Popular</label>
                        </div>
                        <div class="form-check form-check-inline">
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
            <span class="fw-semibold d-none d-sm-inline">Creando nuevo diseño</span>
        </div>
        <div>
            <a href="<?php echo e(route('tattoo.disenos.index')); ?>" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="disenoForm" class="btn-save">
                <i class="bi bi-check-lg me-2"></i>Guardar Diseño
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/tattoo/disenos/create.blade.php ENDPATH**/ ?>