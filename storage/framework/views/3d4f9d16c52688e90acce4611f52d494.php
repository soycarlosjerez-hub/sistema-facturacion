<?php $__env->startSection('title', 'Nuevo Ticket de Garantía'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.ui-page { --accent: #06b6d4; --accent-rgb: 6,182,212; --accent-hover: #0891b2; }

body.dark-mode .ui-card-title { color: #f1f5f9; }
body.dark-mode .ui-card-subtitle { color: #94a3b8; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#06b6d4;--accent-rgb:6,182,212;--accent-hover:#0891b2;">

    
    <div class="ui-header" style="--delay:0s;">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-ticket-perforated"></i>
                </div>
                <div>
                    <h1 class="ui-header-title">Nuevo Ticket de Garantía</h1>
                    <div class="ui-header-meta">
                        <span>Crear un nuevo ticket para gestión de garantía</span>
                        <span class="divider">·</span>
                        <a href="<?php echo e(route('climatizacion.tickets-garantia.index')); ?>" style="color:rgba(255,255,255,.8);text-decoration:none;">
                            <i class="bi bi-arrow-left"></i> Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="ui-card" style="--delay:.1s;max-width:900px;margin:0 auto;">
        <div style="height:4px;background:linear-gradient(90deg, #06b6d4, rgba(255,255,255,.3));"></div>
        <div style="padding:1.25rem 1.75rem 0;">
            <div class="ui-card-title" style="padding:0;margin-bottom:.15rem;">
                <i class="bi bi-file-earmark-plus"></i> Datos del Ticket
            </div>
            <div class="ui-card-subtitle" style="padding:0;">Completa la información para registrar el ticket</div>
        </div>

        <div class="ui-card-body">
            <form action="<?php echo e(route('climatizacion.tickets-garantia.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="cliente_id">Cliente <span class="text-danger">*</span></label>
                        <select name="cliente_id" id="cliente_id"
                                class="ui-select <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar cliente</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e(old('cliente_id') == $c->id ? 'selected' : ''); ?>>
                                    <?php echo e($c->nombre); ?> <?php echo e($c->identificacion ? ' - '.$c->identificacion : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="producto_id">Producto</label>
                        <select name="producto_id" id="producto_id"
                                class="ui-select <?php $__errorArgs = ['producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin producto específico</option>
                            <?php $__currentLoopData = $productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php echo e(old('producto_id') == $p->id ? 'selected' : ''); ?>>
                                    <?php echo e($p->nombre); ?> <?php echo e($p->codigo ? '('.$p->codigo.')' : ''); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['producto_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="instalacion_id">Instalación Relacionada</label>
                        <select name="instalacion_id" id="instalacion_id"
                                class="ui-select <?php $__errorArgs = ['instalacion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin instalación</option>
                            <?php $__currentLoopData = $instalaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($inst->id); ?>" <?php echo e(old('instalacion_id') == $inst->id ? 'selected' : ''); ?>>
                                    #<?php echo e($inst->id); ?> - <?php echo e($inst->cliente?->nombre ?? 'N/A'); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['instalacion_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="tipo_garantia">Tipo de Garantía <span class="text-danger">*</span></label>
                        <select name="tipo_garantia" id="tipo_garantia"
                                class="ui-select <?php $__errorArgs = ['tipo_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar tipo</option>
                            <?php $__currentLoopData = \App\Models\TicketGarantia::TIPOS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($val); ?>" <?php echo e(old('tipo_garantia') === $val ? 'selected' : ''); ?>>
                                    <?php echo e($label); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tipo_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_compra">Fecha de Compra <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_compra" id="fecha_compra"
                               class="ui-input <?php $__errorArgs = ['fecha_compra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('fecha_compra', date('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['fecha_compra'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label" for="fecha_vencimiento_garantia">Fecha Vencimiento Garantía <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_vencimiento_garantia" id="fecha_vencimiento_garantia"
                               class="ui-input <?php $__errorArgs = ['fecha_vencimiento_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('fecha_vencimiento_garantia')); ?>" required>
                        <div class="form-text text-muted small">Debe ser posterior a la fecha de compra</div>
                        <?php $__errorArgs = ['fecha_vencimiento_garantia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="mb-3">
                    <label class="ui-label" for="descripcion_problema">Descripción del Problema <span class="text-danger">*</span></label>
                    <textarea name="descripcion_problema" id="descripcion_problema" rows="4"
                              class="ui-textarea <?php $__errorArgs = ['descripcion_problema'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                              placeholder="Describe detalladamente el problema reportado por el cliente…"><?php echo e(old('descripcion_problema')); ?></textarea>
                    <?php $__errorArgs = ['descripcion_problema'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                
                <div class="row g-3 mb-2">
                    <div class="col-md-6">
                        <label class="ui-label" for="tecnico_asignado_id">Técnico Asignado</label>
                        <select name="tecnico_asignado_id" id="tecnico_asignado_id"
                                class="ui-select <?php $__errorArgs = ['tecnico_asignado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Sin asignar</option>
                            <?php $__currentLoopData = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'tecnico'))->orWhere('id', old('tecnico_asignado_id'))->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>" <?php echo e(old('tecnico_asignado_id') == $u->id ? 'selected' : ''); ?>>
                                    <?php echo e($u->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['tecnico_asignado_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="ui-sticky-bar" style="position:sticky;bottom:0;left:0;right:0;background:rgba(255,255,255,.85);backdrop-filter:blur(20px);border-top:2px solid #06b6d4;padding:.7rem 1.5rem;z-index:1050;box-shadow:0 -4px 20px rgba(0,0,0,.08);margin:0 -1.75rem -1.5rem;border-radius:0 0 var(--radius-2xl) var(--radius-2xl);">
                    <div class="ui-sticky-bar-inner">
                        <a href="<?php echo e(route('climatizacion.tickets-garantia.index')); ?>" class="ui-btn ui-btn-ghost">
                            <i class="bi bi-x-lg"></i> Cancelar
                        </a>
                        <button type="submit" class="ui-btn ui-btn-solid">
                            <i class="bi bi-check-lg"></i> Crear Ticket
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/climatizacion/garantias/create.blade.php ENDPATH**/ ?>