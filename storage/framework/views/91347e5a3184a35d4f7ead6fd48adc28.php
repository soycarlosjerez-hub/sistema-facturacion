<?php $__env->startSection('title', 'Editar Servicio de Domótica'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.form-section-title {
    font-size: .75rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid #e2e8f0;
}
body.dark-mode .form-section-title { color: #94a3b8; border-bottom-color: #1e293b; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-hdd-network"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar Servicio</h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil-square me-1"></i>
                        <?php echo e($servicio->numero_proyecto); ?> · <?php echo e($servicio->titulo); ?>

                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('domotica.show', $servicio)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
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

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(6,182,212,.05);border-left:4px solid #06b6d4 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#06b6d4;background:rgba(6,182,212,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando el servicio:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;"><?php echo e($servicio->numero_proyecto); ?> · <?php echo e($servicio->titulo); ?></strong>
            </div>
        </div>
    </div>

    <form id="domoticaForm" method="POST" action="<?php echo e(route('domotica.update', $servicio)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="ui-card mb-4" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-person-vcard"></i> Información del Cliente</div>
            <div class="ui-card-subtitle">Selecciona el cliente del servicio</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" class="ui-select <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $servicio->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                    <?php echo e($cliente->nombre); ?> - <?php echo e($cliente->rnc_cedula); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Tipo de Servicio <span class="text-danger">*</span></label>
                        <select name="tipo_servicio" class="ui-select <?php $__errorArgs = ['tipo_servicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar...</option>
                            <option value="camaras_seguridad" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'camaras_seguridad' ? 'selected' : ''); ?>>Cámaras de Seguridad</option>
                            <option value="alarmas" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'alarmas' ? 'selected' : ''); ?>>Alarmas</option>
                            <option value="control_acceso" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'control_acceso' ? 'selected' : ''); ?>>Control de Acceso</option>
                            <option value="redes" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'redes' ? 'selected' : ''); ?>>Redes</option>
                            <option value="automatizacion" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'automatizacion' ? 'selected' : ''); ?>>Automatización</option>
                            <option value="sonido" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'sonido' ? 'selected' : ''); ?>>Sonido</option>
                            <option value="iluminacion" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'iluminacion' ? 'selected' : ''); ?>>Iluminación</option>
                            <option value="otro" <?php echo e(old('tipo_servicio', $servicio->tipo_servicio) == 'otro' ? 'selected' : ''); ?>>Otro</option>
                        </select>
                        <?php $__errorArgs = ['tipo_servicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.2s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-clipboard-check"></i> Detalles del Servicio</div>
            <div class="ui-card-subtitle">Describe el proyecto y la instalación</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="ui-input <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('titulo', $servicio->titulo)); ?>" required>
                        <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin asignar</option>
                            <?php $__currentLoopData = $tecnicos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tech): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tech->id); ?>" <?php echo e(old('tecnico_id', $servicio->equipo_asignado_id) == $tech->id ? 'selected' : ''); ?>>
                                    <?php echo e($tech->nombre); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-12">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" class="ui-input <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('descripcion', $servicio->descripcion)); ?></textarea>
                        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Dirección de Instalación</label>
                        <input type="text" name="direccion_instalacion" class="ui-input <?php $__errorArgs = ['direccion_instalacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('direccion_instalacion', $servicio->direccion_instalacion)); ?>">
                        <?php $__errorArgs = ['direccion_instalacion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Fecha Programada</label>
                        <input type="date" name="fecha_programada" class="ui-input <?php $__errorArgs = ['fecha_programada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('fecha_programada', $servicio->fecha_programada ? $servicio->fecha_programada->format('Y-m-d') : '')); ?>">
                        <?php $__errorArgs = ['fecha_programada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.3s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-currency-dollar"></i> Presupuesto</div>
            <div class="ui-card-subtitle">Montos del servicio</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="ui-label">Presupuesto</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="presupuesto" class="ui-input" step="0.01" min="0" value="<?php echo e(old('presupuesto', $servicio->presupuesto)); ?>">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Descuento</label>
                        <div class="input-group">
                            <span class="input-group-text">RD$</span>
                            <input type="number" name="descuento" class="ui-input" step="0.01" min="0" value="<?php echo e(old('descuento', $servicio->descuento)); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ui-card mb-4" style="--delay:.4s">
            <div class="ui-card-accent"></div>
            <div class="ui-card-title"><i class="bi bi-journal-text"></i> Notas</div>
            <div class="ui-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="ui-label">Notas Adicionales</label>
                        <textarea name="notas" class="ui-input" rows="3"><?php echo e(old('notas', $servicio->notas)); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#06b6d4;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando servicio: <?php echo e($servicio->numero_proyecto); ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('domotica.show', $servicio)); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="domoticaForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-check-lg me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/domotica/edit.blade.php ENDPATH**/ ?>