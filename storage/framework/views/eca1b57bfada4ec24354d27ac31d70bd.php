<?php $__env->startSection('title', 'Editar Secuencia e-CF'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-sticky-bar { border-top-color: #f59e0b; }
body.dark-mode .ui-sticky-bar .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .ui-sticky-bar .btn-save:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .ui-card .ui-input:focus,
body.dark-mode .ui-card .ui-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .ui-card .ui-btn-solid { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .ui-card .ui-btn-solid:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4 ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>    <div class="bubble"></div>    <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle"><i class="bi bi-list-ol"></i></div>
                <div>
                    <h4 class="ui-header-title">Editar Secuencia e-CF</h4>
                    <div class="ui-header-meta"><i class="bi bi-info-circle me-1"></i> <span><?php echo e($secuencia->tipo_ecf); ?> - <?php echo e($secuencia->nombre); ?></span></div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('secuencias-ecf.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill"><i class="bi bi-arrow-left me-1"></i> Volver</a>
            </div>
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

    <div class="ui-card mb-5" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <form id="secuenciaEcfForm" action="<?php echo e(route('secuencias-ecf.update', $secuencia)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información de la Secuencia
                    </h6>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Tipo de e-CF</label>
                        <select name="tipo_ecf" class="ui-select rounded-3" required>
                            <?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $nombre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php echo e($secuencia->tipo_ecf === $key ? 'selected' : ''); ?>>
                                    <?php echo e($key); ?> - <?php echo e($nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Nombre Descriptivo</label>
                        <input type="text" name="nombre" class="ui-input rounded-3" value="<?php echo e($secuencia->nombre); ?>" required>
                    </div>

                    <div class="col-md-12">
                        <label class="ui-label">Descripción</label>
                        <input type="text" name="descripcion" class="ui-input rounded-3" value="<?php echo e($secuencia->descripcion); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="ui-label">Desde</label>
                        <input type="number" name="desde" class="ui-input rounded-3" min="1" value="<?php echo e($secuencia->desde); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Hasta</label>
                        <input type="number" name="hasta" class="ui-input rounded-3" min="1" value="<?php echo e($secuencia->hasta); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Actual</label>
                        <input type="number" name="actual" class="ui-input rounded-3" min="0" value="<?php echo e($secuencia->actual); ?>" required>
                        <small class="text-muted">Cuidado: modificar esto puede generar saltos o duplicados.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input rounded-3" value="<?php echo e($secuencia->fecha_vencimiento->format('Y-m-d')); ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label class="ui-label">Estado</label>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" <?php echo e($secuencia->activo ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="activo">Activa</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Editando: <?php echo e($secuencia->tipo_ecf); ?></span>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('secuencias-ecf.index')); ?>" class="btn-cancel rounded-pill px-4">Cancelar</a>
            <button type="submit" form="secuenciaEcfForm" class="btn-save rounded-pill px-4 shadow-sm">
                <i class="bi bi-save me-1"></i> Actualizar Secuencia
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/secuencias-ecf/edit.blade.php ENDPATH**/ ?>