<?php $__env->startSection('title', 'Editar ' . $orden->codigo); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
body.dark-mode .ui-page { --accent:#ef4444; --accent-rgb:239,68,68; --accent-hover:#dc2626; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#ef4444;--accent-rgb:239,68,68;--accent-hover:#dc2626;">

    
    <div class="ui-header" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar: <?php echo e($orden->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>Modificar orden de emergencia
                        <span class="divider">·</span>
                        <span class="ui-badge <?php echo e(match($orden->estado) {
                            'reportada' => 'ui-badge-danger',
                            'asignada'  => 'ui-badge-warning',
                            'en_camino' => 'ui-badge-primary',
                            'en_lugar'  => 'ui-badge-info',
                            'resuelta'  => 'ui-badge-success',
                            'cerrada'   => 'ui-badge-neutral',
                            default     => 'ui-badge-neutral',
                        }); ?>" style="font-size:.72rem;">
                            <?php echo e(\App\Models\OrdenEmergencia::ESTADOS[$orden->estado] ?? $orden->estado); ?>

                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('climatizacion.ordenes-emergencia.show', $orden)); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-eye me-1"></i> Ver
                </a>
                <a href="<?php echo e(route('climatizacion.ordenes-emergencia.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('climatizacion.ordenes-emergencia.update', $orden)); ?>" method="POST" id="form-emergencia">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        <div class="row g-4">

            
            <div class="col-lg-7">

                <div class="ui-card" style="--delay:.1s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-info-circle"></i> Información de la Emergencia
                    </div>
                    <div class="ui-card-subtitle">Datos generales del reporte</div>
                    <div class="ui-card-body">
                        <div class="row g-3">

                            
                            <div class="col-md-6">
                                <label class="ui-label">Código</label>
                                <input type="text" class="ui-input" value="<?php echo e($orden->codigo); ?>" disabled readonly
                                       style="background:#f8fafc;opacity:.8;">
                            </div>

                            
                            <div class="col-md-6">
                                <label class="ui-label">Estado <span class="text-danger">*</span></label>
                                <select name="estado" class="ui-select <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <?php $__currentLoopData = \App\Models\OrdenEmergencia::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('estado', $orden->estado) === $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-6">
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
                                        <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $orden->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                            <?php echo e($cliente->nombre); ?> <?php echo e($cliente->identificacion ? '('. $cliente->identificacion .')' : ''); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-3">
                                <label class="ui-label">Prioridad <span class="text-danger">*</span></label>
                                <select name="prioridad" class="ui-select <?php $__errorArgs = ['prioridad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Seleccionar...</option>
                                    <?php $__currentLoopData = \App\Models\OrdenEmergencia::PRIORIDADES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('prioridad', $orden->prioridad) === $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['prioridad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-3">
                                <label class="ui-label">Tipo de Falla <span class="text-danger">*</span></label>
                                <select name="tipo_falla" class="ui-select <?php $__errorArgs = ['tipo_falla'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Seleccionar...</option>
                                    <?php $__currentLoopData = \App\Models\OrdenEmergencia::TIPOS_FALLA; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e(old('tipo_falla', $orden->tipo_falla) === $key ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['tipo_falla'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-8">
                                <label class="ui-label">Dirección <span class="text-danger">*</span></label>
                                <input type="text" name="direccion" class="ui-input <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('direccion', $orden->direccion)); ?>" placeholder="Dirección del servicio" required>
                                <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-md-4">
                                <label class="ui-label">Teléfono de Contacto</label>
                                <input type="text" name="contacto_telefono" class="ui-input <?php $__errorArgs = ['contacto_telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('contacto_telefono', $orden->contacto_telefono)); ?>" placeholder="809-000-0000">
                                <?php $__errorArgs = ['contacto_telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            
                            <div class="col-12">
                                <label class="ui-label">Descripción <span class="text-danger">*</span></label>
                                <textarea name="descripcion" rows="4" class="ui-textarea <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                          placeholder="Describa el problema reportado..." required><?php echo e(old('descripcion', $orden->descripcion)); ?></textarea>
                                <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-5">

                
                <div class="ui-card" style="--delay:.15s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-person-badge"></i> Asignación
                    </div>
                    <div class="ui-card-body">
                        <label class="ui-label">Técnico Asignado</label>
                        <select name="tecnico_id" class="ui-select <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin asignar...</option>
                            <?php $__currentLoopData = $tecnicos ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tecnico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tecnico->id); ?>" <?php echo e(old('tecnico_id', $orden->tecnico_id) == $tecnico->id ? 'selected' : ''); ?>>
                                    <?php echo e($tecnico->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tecnico_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle me-1"></i>
                            Al cambiar el técnico, la orden se actualizará automáticamente.
                        </small>
                    </div>
                </div>

                
                <div class="ui-card" style="--delay:.2s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-currency-dollar"></i> Costos
                    </div>
                    <div class="ui-card-body">
                        <div class="mb-3">
                            <label class="ui-label">Costo Estimado</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0" name="costo_estimado"
                                       class="ui-input <?php $__errorArgs = ['costo_estimado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('costo_estimado', $orden->costo_estimado)); ?>" placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['costo_estimado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label class="ui-label">Costo Final</label>
                            <div class="ui-input-group">
                                <span class="ui-input-group-text">RD$</span>
                                <input type="number" step="0.01" min="0" name="costo_final"
                                       class="ui-input <?php $__errorArgs = ['costo_final'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       value="<?php echo e(old('costo_final', $orden->costo_final)); ?>" placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['costo_final'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                
                <div class="ui-card" style="--delay:.25s">
                    <div class="ui-card-accent"></div>
                    <div class="ui-card-title">
                        <i class="bi bi-clock-history"></i> Auditoría
                    </div>
                    <div class="ui-card-body">
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Creado por</span>
                            <span class="ui-detail-value"><?php echo e($orden->creadoPor?->name ?? '—'); ?></span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Creado</span>
                            <span class="ui-detail-value"><?php echo e($orden->created_at?->format('d/m/Y h:i A') ?? '—'); ?></span>
                        </div>
                        <div class="ui-detail-row">
                            <span class="ui-detail-label">Actualizado</span>
                            <span class="ui-detail-value"><?php echo e($orden->updated_at?->format('d/m/Y h:i A') ?? '—'); ?></span>
                        </div>
                        <?php if($orden->sla_deadline): ?>
                            <div class="ui-detail-row">
                                <span class="ui-detail-label">SLA Deadline</span>
                                <span class="ui-detail-value"><?php echo e($orden->sla_deadline->format('d/m/Y h:i A')); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('climatizacion.ordenes-emergencia.show', $orden)); ?>" class="ui-btn ui-btn-ghost rounded-pill">
                    Cancelar
                </a>
                <button type="submit" form="form-emergencia" class="ui-btn ui-btn-solid rounded-pill px-5">
                    <i class="bi bi-check-lg me-2"></i> Actualizar
                </button>
            </div>
        </div>

    </form>

    
    <div style="height:80px;"></div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/emergencias/edit.blade.php ENDPATH**/ ?>