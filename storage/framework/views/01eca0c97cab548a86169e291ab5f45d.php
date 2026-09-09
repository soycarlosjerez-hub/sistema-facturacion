<?php $__env->startSection('title', 'Editar Mantenimiento #' . $mantenimiento->numero); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
    .repuesto-row {
        background: rgba(248,250,252,.6);
        border-radius: var(--radius);
        padding: 1rem;
        margin-bottom: .75rem;
        border: 1px solid #f1f5f9;
        transition: all .2s ease;
        position: relative;
    }
    .repuesto-row:hover {
        border-color: #e2e8f0;
        background: rgba(248,250,252,.9);
    }
    .repuesto-row .remove-repuesto {
        position: absolute;
        top: .5rem;
        right: .5rem;
    }
    body.dark-mode .repuesto-row {
        background: rgba(15,23,42,.4);
        border-color: #1e293b;
    }
    body.dark-mode .repuesto-row:hover {
        background: rgba(15,23,42,.6);
        border-color: #334155;
    }
    .subtotal-display {
        font-size: .85rem;
        color: var(--accent, #3b82f6);
        font-weight: 700;
    }

    .estado-selector {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .estado-option {
        padding: .5rem 1rem;
        border-radius: var(--radius-pill);
        border: 2px solid #e2e8f0;
        background: transparent;
        cursor: pointer;
        font-size: .85rem;
        font-weight: 500;
        transition: all .2s ease;
        color: #64748b;
    }
    .estado-option:hover {
        border-color: #94a3b8;
        color: #334155;
    }
    .estado-option.active {
        border-color: var(--accent, #3b82f6);
        background: rgba(var(--accent-rgb, 59,130,246),.08);
        color: var(--accent, #3b82f6);
        font-weight: 700;
    }
    body.dark-mode .estado-option {
        border-color: #334155;
        color: #94a3b8;
    }
    body.dark-mode .estado-option:hover {
        border-color: #475569;
        color: #f1f5f9;
    }
    body.dark-mode .estado-option.active {
        border-color: var(--accent, #3b82f6);
        background: rgba(var(--accent-rgb, 59,130,246),.15);
        color: #60a5fa;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#3b82f6;--accent-rgb:59,130,246;--accent-hover:#2563eb;">
    
    <div class="ui-header">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Editar: <?php echo e($mantenimiento->numero); ?></h1>
                    <div class="ui-header-meta">
                        <span>Modificar mantenimiento de equipo</span>
                        <span class="divider">|</span>
                        <span>
                            <?php
                                $estadoColor = match ($mantenimiento->estado) {
                                    'pendiente' => 'neutral',
                                    'programada' => 'info',
                                    'en_curso' => 'warning',
                                    'completado' => 'success',
                                    'cancelado' => 'danger',
                                    default => 'neutral',
                                };
                            ?>
                            <span class="ui-badge ui-badge-<?php echo e($estadoColor); ?>">
                                <?php echo e(\App\Models\Mantenimiento::ESTADOS[$mantenimiento->estado] ?? $mantenimiento->estado); ?>

                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('climatizacion.mantenimientos.show', $mantenimiento)); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-eye"></i> Ver Detalles
                </a>
                <a href="<?php echo e(route('climatizacion.mantenimientos.index')); ?>" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('climatizacion.mantenimientos.update', $mantenimiento)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

        
        <div class="ui-card" style="--delay:.03s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0 0 .75rem 0;">
                    <i class="bi bi-flag"></i> Estado del Mantenimiento
                </h5>
                <div class="estado-selector">
                    <?php $__currentLoopData = \App\Models\Mantenimiento::ESTADOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isActive = old('estado', $mantenimiento->estado) === $val;
                        ?>
                        <label class="estado-option <?php echo e($isActive ? 'active' : ''); ?>"
                               data-value="<?php echo e($val); ?>"
                               onclick="document.getElementById('estado_input').value='<?php echo e($val); ?>'; document.querySelectorAll('.estado-option').forEach(e=>e.classList.remove('active')); this.classList.add('active');">
                            <span class="timeline-dot <?php echo e($val); ?>" style="display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:.35rem;
                                background:<?php echo e($val === 'pendiente' ? '#94a3b8' : ($val === 'programada' ? '#3b82f6' : ($val === 'en_curso' ? '#f59e0b' : ($val === 'completado' ? '#10b981' : '#ef4444')))); ?>;">
                            </span>
                            <?php echo e($label); ?>

                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <input type="hidden" name="estado" id="estado_input" value="<?php echo e(old('estado', $mantenimiento->estado)); ?>">
                </div>
                <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-2"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.05s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0 0 1rem 0;">
                    <i class="bi bi-info-circle"></i> Información General
                </h5>

                <div class="row g-3">
                    
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
                            <option value="">Seleccionar cliente</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cliente->id); ?>" <?php echo e(old('cliente_id', $mantenimiento->cliente_id) == $cliente->id ? 'selected' : ''); ?>>
                                    <?php echo e($cliente->nombre); ?>

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
                        <label class="ui-label">Tipo <span class="text-danger">*</span></label>
                        <select name="tipo" class="ui-select <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar</option>
                            <?php $__currentLoopData = \App\Models\Mantenimiento::TIPOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('tipo', $mantenimiento->tipo) === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-3">
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
                            <?php $__currentLoopData = \App\Models\User::where('activo', true)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tecnico): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($tecnico->id); ?>" <?php echo e(old('tecnico_id', $mantenimiento->tecnico_id) == $tecnico->id ? 'selected' : ''); ?>>
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
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    
                    <div class="col-md-6">
                        <label class="ui-label">Contrato de Mantenimiento</label>
                        <select name="contrato_mantenimiento_id" class="ui-select <?php $__errorArgs = ['contrato_mantenimiento_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin contrato asociado</option>
                            <?php $__currentLoopData = $contratos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $contrato): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($contrato->id); ?>" <?php echo e(old('contrato_mantenimiento_id', $mantenimiento->contrato_mantenimiento_id) == $contrato->id ? 'selected' : ''); ?>>
                                    <?php echo e($contrato->codigo); ?> — <?php echo e($contrato->cliente?->nombre ?? 'Cliente #'.$contrato->cliente_id); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['contrato_mantenimiento_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-3">
                        <label class="ui-label">Programada para</label>
                        <?php
                            $progValue = old('programada_para', optional($mantenimiento->programada_para)->format('Y-m-d\TH:i'));
                        ?>
                        <input type="datetime-local" name="programada_para"
                               class="ui-input <?php $__errorArgs = ['programada_para'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e($progValue); ?>">
                        <?php $__errorArgs = ['programada_para'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-3">
                        <label class="ui-label">Completada en</label>
                        <?php
                            $compValue = old('completada_en', optional($mantenimiento->completada_en)->format('Y-m-d\TH:i'));
                        ?>
                        <input type="datetime-local" name="completada_en"
                               class="ui-input <?php $__errorArgs = ['completada_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e($compValue); ?>">
                        <?php $__errorArgs = ['completada_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    
                    <div class="col-md-6">
                        <label class="ui-label">Descripción de la Falla / Trabajo</label>
                        <textarea name="descripcion_falla" rows="3"
                                  class="ui-textarea <?php $__errorArgs = ['descripcion_falla'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Describa el problema reportado..."><?php echo e(old('descripcion_falla', $mantenimiento->descripcion_falla)); ?></textarea>
                        <?php $__errorArgs = ['descripcion_falla'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label class="ui-label">Solución Aplicada</label>
                        <textarea name="solucion_aplicada" rows="3"
                                  class="ui-textarea <?php $__errorArgs = ['solucion_aplicada'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Describa la solución aplicada..."><?php echo e(old('solucion_aplicada', $mantenimiento->solucion_aplicada)); ?></textarea>
                        <?php $__errorArgs = ['solucion_aplicada'];
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

        
        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <h5 class="ui-card-title" style="padding:0 0 1rem 0;">
                    <i class="bi bi-cash-stack"></i> Costos
                </h5>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="ui-label">Costo de Repuestos (RD$)</label>
                        <input type="number" step="0.01" min="0" name="costo_repuestos"
                               class="ui-input <?php $__errorArgs = ['costo_repuestos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('costo_repuestos', $mantenimiento->costo_repuestos ?? 0)); ?>" id="costo_repuestos">
                        <?php $__errorArgs = ['costo_repuestos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label">Mano de Obra (RD$)</label>
                        <input type="number" step="0.01" min="0" name="mano_de_obra"
                               class="ui-input <?php $__errorArgs = ['mano_de_obra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('mano_de_obra', $mantenimiento->mano_de_obra ?? 0)); ?>" id="mano_de_obra">
                        <?php $__errorArgs = ['mano_de_obra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="w-100">
                            <label class="ui-label">Total Estimado</label>
                            <div class="ui-input" style="background:#f8fafc;font-weight:700;font-size:1.1rem;color:var(--accent);" id="total_estimado">
                                RD$ <?php echo e(number_format(($mantenimiento->costo_repuestos ?? 0) + ($mantenimiento->mano_de_obra ?? 0), 2)); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.15s;">
            <div class="ui-card-accent"></div>
            <div class="ui-card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="ui-card-title" style="padding:0;">
                        <i class="bi bi-box-seam"></i> Repuestos Utilizados
                    </h5>
                    <button type="button" class="ui-btn ui-btn-sm ui-btn-ghost" id="addRepuestoBtn">
                        <i class="bi bi-plus-lg"></i> Agregar Repuesto
                    </button>
                </div>

                <div id="repuestosContainer">
                    
                    <?php
                        $repuestos = old('repuestos_usados', $mantenimiento->repuestos_usados ?? []);
                    ?>
                    <?php $__currentLoopData = $repuestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $repuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="repuesto-row" data-index="<?php echo e($idx); ?>">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-repuesto" title="Eliminar repuesto" style="position:absolute;top:.5rem;right:.5rem;padding:.15rem .4rem;">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label class="ui-label small">Nombre del Repuesto</label>
                                <input type="text" name="repuestos_usados[<?php echo e($idx); ?>][nombre]"
                                       class="ui-input" value="<?php echo e($repuesto['nombre'] ?? ''); ?>"
                                       placeholder="Ej: Filtro de aire">
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small">Cantidad</label>
                                <input type="number" name="repuestos_usados[<?php echo e($idx); ?>][cantidad]"
                                       class="ui-input repuesto-cantidad" value="<?php echo e($repuesto['cantidad'] ?? 1); ?>" min="1">
                            </div>
                            <div class="col-md-3">
                                <label class="ui-label small">Precio Unit. (RD$)</label>
                                <input type="number" step="0.01" name="repuestos_usados[<?php echo e($idx); ?>][precio]"
                                       class="ui-input repuesto-precio" value="<?php echo e($repuesto['precio'] ?? 0); ?>" min="0">
                            </div>
                            <div class="col-md-2">
                                <label class="ui-label small">Subtotal</label>
                                <div class="subtotal-display">
                                    RD$ <?php echo e(number_format(($repuesto['cantidad'] ?? 0) * ($repuesto['precio'] ?? 0), 2)); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div id="noRepuestos" class="ui-empty-state <?php echo e(count($repuestos) > 0 ? 'd-none' : ''); ?>" style="padding:1.5rem;">
                    <i class="bi bi-box" style="font-size:1.5rem;"></i>
                    <p style="font-size:.9rem;">No se han agregado repuestos</p>
                    <span class="text-muted small">Haz clic en "Agregar Repuesto" para detallar las piezas utilizadas</span>
                </div>
            </div>
        </div>

        
        <div class="ui-sticky-bar">
            <div class="ui-sticky-bar-inner">
                <a href="<?php echo e(route('climatizacion.mantenimientos.show', $mantenimiento)); ?>" class="ui-btn ui-btn-ghost">
                    <i class="bi bi-x-lg"></i> Cancelar
                </a>
                <button type="submit" class="ui-btn ui-btn-solid">
                    <i class="bi bi-check-lg"></i> Actualizar Mantenimiento
                </button>
            </div>
        </div>
    </form>

    
    <div style="height:80px;"></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('repuestosContainer');
    const noRepuestos = document.getElementById('noRepuestos');
    const addBtn = document.getElementById('addRepuestoBtn');
    const costInput = document.getElementById('costo_repuestos');
    const laborInput = document.getElementById('mano_de_obra');
    const totalDisplay = document.getElementById('total_estimado');
    let repuestoIndex = container.children.length;

    // Calculate total
    function updateTotal() {
        const cost = parseFloat(costInput.value) || 0;
        const labor = parseFloat(laborInput.value) || 0;
        totalDisplay.textContent = 'RD$ ' + (cost + labor).toFixed(2);
    }
    costInput.addEventListener('input', updateTotal);
    laborInput.addEventListener('input', updateTotal);

    // Create repuesto row HTML
    function createRepuestoRow(index, data) {
        data = data || {};
        const div = document.createElement('div');
        div.className = 'repuesto-row';
        div.dataset.index = index;
        div.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger remove-repuesto" title="Eliminar repuesto" style="position:absolute;top:.5rem;right:.5rem;padding:.15rem .4rem;">
                <i class="bi bi-x"></i>
            </button>
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="ui-label small">Nombre del Repuesto</label>
                    <input type="text" name="repuestos_usados[${index}][nombre]"
                           class="ui-input" value="${data.nombre || ''}"
                           placeholder="Ej: Filtro de aire">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Cantidad</label>
                    <input type="number" name="repuestos_usados[${index}][cantidad]"
                           class="ui-input repuesto-cantidad" value="${data.cantidad || 1}" min="1">
                </div>
                <div class="col-md-3">
                    <label class="ui-label small">Precio Unit. (RD$)</label>
                    <input type="number" step="0.01" name="repuestos_usados[${index}][precio]"
                           class="ui-input repuesto-precio" value="${data.precio || 0}" min="0">
                </div>
                <div class="col-md-2">
                    <label class="ui-label small">Subtotal</label>
                    <div class="subtotal-display">RD$ 0.00</div>
                </div>
            </div>
        `;

        // Remove button
        div.querySelector('.remove-repuesto').addEventListener('click', function () {
            div.remove();
            toggleEmptyState();
            updateCostFromRepuestos();
        });

        // Subtotal calculation
        const cantInput = div.querySelector('.repuesto-cantidad');
        const precInput = div.querySelector('.repuesto-precio');
        const subDisplay = div.querySelector('.subtotal-display');

        function updateSubtotal() {
            const cant = parseFloat(cantInput.value) || 0;
            const prec = parseFloat(precInput.value) || 0;
            subDisplay.textContent = 'RD$ ' + (cant * prec).toFixed(2);
            updateCostFromRepuestos();
        }

        cantInput.addEventListener('input', updateSubtotal);
        precInput.addEventListener('input', updateSubtotal);
        updateSubtotal();

        return div;
    }

    // Add repuesto
    addBtn.addEventListener('click', function () {
        const row = createRepuestoRow(repuestoIndex++);
        container.appendChild(row);
        toggleEmptyState();
    });

    // Toggle empty state
    function toggleEmptyState() {
        const hasRows = container.querySelectorAll('.repuesto-row').length > 0;
        noRepuestos.classList.toggle('d-none', hasRows);
    }

    // Update costo_repuestos from sum of repuesto line totals
    function updateCostFromRepuestos() {
        let total = 0;
        container.querySelectorAll('.repuesto-row').forEach(function (row) {
            const cant = parseFloat(row.querySelector('.repuesto-cantidad')?.value) || 0;
            const prec = parseFloat(row.querySelector('.repuesto-precio')?.value) || 0;
            total += cant * prec;
        });
        costInput.value = total.toFixed(2);
        updateTotal();
    }

    // Init existing old rows subtotals
    container.querySelectorAll('.repuesto-row').forEach(function (row) {
        const removeBtn = row.querySelector('.remove-repuesto');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
                toggleEmptyState();
                updateCostFromRepuestos();
            });
        }
        const cantInput = row.querySelector('.repuesto-cantidad');
        const precInput = row.querySelector('.repuesto-precio');
        const subDisplay = row.querySelector('.subtotal-display');

        function updateSubtotal() {
            const cant = parseFloat(cantInput.value) || 0;
            const prec = parseFloat(precInput.value) || 0;
            subDisplay.textContent = 'RD$ ' + (cant * prec).toFixed(2);
            updateCostFromRepuestos();
        }
        if (cantInput) cantInput.addEventListener('input', updateSubtotal);
        if (precInput) precInput.addEventListener('input', updateSubtotal);
        if (cantInput && precInput) updateSubtotal();
    });

    toggleEmptyState();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/mantenimientos/edit.blade.php ENDPATH**/ ?>