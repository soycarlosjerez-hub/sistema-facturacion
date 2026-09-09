<?php $__env->startSection('title', 'Editar Certificado'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .premium-header { background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e); }
body.dark-mode .premium-sticky-bar { border-top-color: #f59e0b; }
body.dark-mode .premium-sticky-bar .btn-save { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-sticky-bar .btn-save:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
body.dark-mode .premium-card .form-control:focus,
body.dark-mode .premium-card .form-select:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.15); }
body.dark-mode .premium-card .btn-primary { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,.3); }
body.dark-mode .premium-card .btn-primary:hover { box-shadow: 0 6px 20px rgba(245,158,11,.45); }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">
    <div class="ui-header mb-4" style="--delay:0s;background: linear-gradient(135deg, #92400e, #b45309, #d97706, #92400e);">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-key"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Certificado</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        <span><?php echo e($cert->nombre); ?></span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('certificados-digitales.index')); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
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
        <form id="certificadoForm" action="<?php echo e(route('certificados-digitales.update', $cert)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0" style="color: #f59e0b;">
                        <i class="bi bi-info-circle me-2"></i>Información del Certificado
                    </h6>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="ui-label">Nombre</label>
                        <input type="text" name="nombre" class="ui-input" value="<?php echo e($cert->nombre); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Entidad Emisora</label>
                        <input type="text" name="emisor_cert" class="ui-input" value="<?php echo e($cert->emisor_cert); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">RNC Emisor</label>
                        <input type="text" name="rnc_emisor" class="ui-input" value="<?php echo e($cert->rnc_emisor); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">RNC Titular</label>
                        <input type="text" name="rnc_titular" class="ui-input" value="<?php echo e($cert->rnc_titular); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Reemplazar Archivo (opcional)</label>
                        <input type="file" name="archivo" class="ui-input" accept=".p12,.pfx">
                        <small class="text-muted">Solo suba un nuevo archivo si desea reemplazar el actual</small>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Nueva Contraseña (opcional)</label>
                        <input type="password" name="password" class="ui-input" placeholder="Solo si la cambió">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Número de Serie</label>
                        <input type="text" name="serial_number" class="ui-input" value="<?php echo e($cert->serial_number); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Emisión</label>
                        <input type="date" name="fecha_emision" class="ui-input" value="<?php echo e($cert->fecha_emision?->format('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label">Fecha de Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="ui-input" value="<?php echo e($cert->fecha_vencimiento->format('Y-m-d')); ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label">Notas</label>
                        <textarea name="notas" class="ui-input" rows="2"><?php echo e($cert->notas); ?></textarea>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" <?php echo e($cert->activo ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="stickySaveBar" class="premium-sticky-bar">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold" style="color: #f59e0b;"><i class="bi bi-key me-1"></i> Editando: <?php echo e($cert->nombre); ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('certificados-digitales.index')); ?>" class="btn-cancel me-2">Cancelar</a>
            <button type="submit" form="certificadoForm" class="btn-save rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-check-circle me-1"></i> Actualizar Certificado
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/certificados-digitales/edit.blade.php ENDPATH**/ ?>