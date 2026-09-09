<?php $__env->startSection('title', 'Editar Caja'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .sticky-save-bar {
        position: fixed;
        bottom: 0;
        left: var(--sidebar-width, 280px);
        right: 0;
        background: #fff;
        border-top: 2px solid #f59e0b;
        padding: 0.75rem 1.5rem;
        z-index: 1050;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
    }
    .sticky-save-bar .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    body.dark-mode .sticky-save-bar {
        background: #0f172a;
        border-top-color: #f59e0b;
    }
    @media (max-width: 1199.98px) {
        .sticky-save-bar { left: 0; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed;">
    <div class="container-fluid px-4">
        <div class="ui-header mb-4" style="--delay:0s">
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="bubble"></div>
            <div class="ui-header-body">
                <div class="ui-header-left">
                    <div class="ui-avatar-circle">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h4 class="ui-header-title">Editar Caja</h4>
                        <div class="ui-header-meta">Modifica los datos de <strong><?php echo e($caja->nombre); ?></strong>.</div>
                    </div>
                </div>
                <div class="ui-header-actions">
                    <a href="<?php echo e(route('cajas.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger rounded-4 shadow-sm mb-4" style="border-left: 4px solid #dc3545 !important;">
                    <div class="d-flex">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1">No se pudo actualizar la caja</h6>
                            <ul class="mb-0 ps-3">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('cajas.update', $caja)); ?>" method="POST" id="instanceForm">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom border-light p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold mb-0"><i class="bi bi-cash-register text-primary me-2"></i>Editando: <?php echo e($caja->nombre); ?></h5>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">ID #<?php echo e($caja->id); ?></span>
                        </div>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="ui-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <div class="ui-input-group ui-input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="ui-input-group-text bg-white border-end-0"><i class="bi bi-tag-fill text-warning"></i></span>
                                    <input type="text" name="nombre" class="ui-input border-start-0 <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required value="<?php echo e(old('nombre', $caja->nombre)); ?>">
                                </div>
                                <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-5">
                                <label class="ui-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Código</label>
                                <div class="ui-input-group ui-input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="ui-input-group-text bg-white border-end-0"><i class="bi bi-upc text-warning"></i></span>
                                    <input type="text" name="codigo" class="ui-input border-start-0 <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('codigo', $caja->codigo)); ?>">
                                </div>
                                <?php $__errorArgs = ['codigo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-12">
                                <label class="ui-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Ubicación</label>
                                <div class="ui-input-group ui-input-group-lg shadow-sm rounded-3 overflow-hidden">
                                    <span class="ui-input-group-text bg-white border-end-0"><i class="bi bi-geo-alt-fill text-warning"></i></span>
                                    <input type="text" name="ubicacion" class="ui-input border-start-0" value="<?php echo e(old('ubicacion', $caja->ubicacion)); ?>">
                                </div>
                            </div>

                            <?php if(isset($sucursales) && $sucursales->count()): ?>
                            <div class="col-12">
                                <label class="ui-label fw-bold text-muted small text-uppercase" style="letter-spacing: 1px;">Sucursal</label>
                                <select name="sucursal_id" class="ui-select ui-select-lg shadow-sm rounded-3">
                                    <option value="">Sin asignar</option>
                                    <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($s->id); ?>" <?php echo e(old('sucursal_id', $caja->sucursal_id) == $s->id ? 'selected' : ''); ?>><?php echo e($s->nombre); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <?php endif; ?>

                            <div class="col-12">
                                <div class="p-3 rounded-3 d-flex align-items-start gap-3 <?php echo e($caja->activo ? '' : ''); ?>" style="background: <?php echo e($caja->activo ? 'rgba(34,197,94,0.08)' : 'rgba(239,68,68,0.08)'); ?>; border: 1px solid <?php echo e($caja->activo ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)'); ?>;">
                                    <div class="form-check form-switch fs-4 m-0">
                                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="activo" <?php echo e(old('activo', $caja->activo) ? 'checked' : ''); ?>>
                                    </div>
                                    <div>
                                        <label class="form-check-label fw-bold mb-0" for="activo"><?php echo e($caja->activo ? 'Caja activa' : 'Caja inactiva'); ?></label>
                                        <small class="d-block text-muted">
                                            <?php if($caja->estado == 'abierta'): ?>
                                                <i class="bi bi-exclamation-triangle text-warning"></i> Esta caja está abierta. Ciérrala antes de desactivarla.
                                            <?php else: ?>
                                                Las cajas inactivas no pueden abrir turnos.
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="p-3 rounded-3" style="background: rgba(139,92,246,0.06); border: 1px solid rgba(139,92,246,0.2);">
                                    <label class="fw-bold mb-3 d-block">
                                        <i class="bi bi-receipt-cutoff me-1"></i>
                                        Tipos de Comprobante Permitidos
                                    </label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <?php
                                            $permitidos = $caja->allowed_comprobante_types ?? ['sin', 'ncf', 'ecf'];
                                            $defaultChecked = is_null(old('allowed_comprobante_types')) && empty($permitidos);
                                            if (old('allowed_comprobante_types')) {
                                                $checkedSin = in_array('sin', old('allowed_comprobante_types')) ? 'checked' : '';
                                                $checkedNcf = in_array('ncf', old('allowed_comprobante_types')) ? 'checked' : '';
                                                $checkedEcf = in_array('ecf', old('allowed_comprobante_types')) ? 'checked' : '';
                                            } else {
                                                $checkedSin = in_array('sin', $permitidos) ? 'checked' : '';
                                                $checkedNcf = in_array('ncf', $permitidos) ? 'checked' : '';
                                                $checkedEcf = in_array('ecf', $permitidos) ? 'checked' : '';
                                            }
                                        ?>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="sin" id="tipo_sin" <?php echo e($checkedSin); ?>>
                                            <span class="small fw-semibold">Sin Comprobante</span>
                                            <small class="text-muted">(B00)</small>
                                        </label>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="ncf" id="tipo_ncf" <?php echo e($checkedNcf); ?>>
                                            <span class="small fw-semibold">NCF</span>
                                            <small class="text-muted">(Tradicional)</small>
                                        </label>
                                        <label class="form-check d-flex align-items-center gap-2 mb-0" style="cursor:pointer;">
                                            <input class="form-check-input" type="checkbox" name="allowed_comprobante_types[]" value="ecf" id="tipo_ecf" <?php echo e($checkedEcf); ?>>
                                            <span class="small fw-semibold">e-CF</span>
                                            <small class="text-muted">(DGII)</small>
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Selecciona los tipos de comprobante que estarán disponibles en la terminal de venta de esta caja.</small>
                                </div>
                            </div>

                            <!-- Info adicional -->
                            <div class="col-12">
                                <div class="row g-2 small text-muted">
                                    <div class="col-md-4">
                                        <i class="bi bi-calendar-plus me-1"></i>Creada: <strong><?php echo e($caja->created_at->format('d/m/Y')); ?></strong>
                                    </div>
                                    <?php if($caja->updated_at && $caja->updated_at != $caja->created_at): ?>
                                    <div class="col-md-4">
                                        <i class="bi bi-pencil me-1"></i>Última edición: <strong><?php echo e($caja->updated_at->diffForHumans()); ?></strong>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-md-4">
                                        <i class="bi bi-info-circle me-1"></i>Estado actual: <strong><?php echo e(ucfirst($caja->estado)); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
        </div>

        <div class="sticky-save-bar">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small d-none d-md-inline">
                    <i class="bi bi-info-circle me-1"></i> Editando caja: <?php echo e($caja->nombre); ?>

                </span>
                <div class="d-flex gap-2 ms-auto">
                    <a href="<?php echo e(route('cajas.index')); ?>" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                    <button type="submit" form="instanceForm" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                        <i class="bi bi-save me-2"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/cajas/edit.blade.php ENDPATH**/ ?>