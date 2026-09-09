<?php $__env->startSection('title', 'Editar Equipo'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#38bdf8;--accent-rgb:56,189,248;--accent-hover:#0ea5e9;">

    <div class="ui-header mb-4">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-phone"></i>
                </div>
                <div>
                    <div class="ui-header-title">Editar Equipo</div>
                    <div class="ui-header-meta">
                        <i class="bi bi-pencil me-1"></i>
                        Actualiza la información del equipo
                    </div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('equipos.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger rounded-4 shadow-sm border-0 mb-4" style="border-left: 4px solid #dc3545 !important;">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="alert rounded-4 shadow-sm border-0 mb-4" style="background:rgba(56,189,248,.05);border-left:4px solid #38bdf8 !important;">
        <div class="d-flex align-items-center">
            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;color:#38bdf8;background:rgba(56,189,248,.1);">
                <i class="bi bi-info-circle fs-5"></i>
            </div>
            <div>
                <span class="text-muted">Editando el equipo:</span>
                <strong class="d-block" style="font-size:1.1rem;color:#1e293b;"><?php echo e($equipo->serial_imei); ?> - <?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></strong>
            </div>
        </div>
    </div>

    <form id="equipoForm" action="<?php echo e(route('equipos.update', $equipo)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="ui-card" style="--delay:.1s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #0891b2;">
                    <i class="bi bi-phone me-2"></i>Identificación del Equipo
                </h6>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Serial / IMEI <span class="text-danger">*</span></label>
                            <input type="text" name="serial_imei" value="<?php echo e(old('serial_imei', $equipo->serial_imei)); ?>" class="ui-input <?php $__errorArgs = ['serial_imei'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Ej. ABC123456789" autocomplete="off">
                            <?php $__errorArgs = ['serial_imei'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Serial ESN</label>
                            <input type="text" name="serial_esn" value="<?php echo e(old('serial_esn', $equipo->serial_esn)); ?>" class="ui-input <?php $__errorArgs = ['serial_esn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ej. ESN001234">
                            <?php $__errorArgs = ['serial_esn'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Marca <span class="text-danger">*</span></label>
                            <input type="text" name="marca" value="<?php echo e(old('marca', $equipo->marca)); ?>" class="ui-input <?php $__errorArgs = ['marca'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Ej. Apple, Samsung">
                            <?php $__errorArgs = ['marca'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" value="<?php echo e(old('modelo', $equipo->modelo)); ?>" class="ui-input <?php $__errorArgs = ['modelo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required placeholder="Ej. iPhone 15 Pro, Galaxy S24">
                            <?php $__errorArgs = ['modelo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Color</label>
                            <input type="text" name="color" value="<?php echo e(old('color', $equipo->color)); ?>" class="ui-input <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ej. Negro, Blanco">
                            <?php $__errorArgs = ['color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Almacenamiento (GB)</label>
                            <input type="number" name="almacenamiento_gb" value="<?php echo e(old('almacenamiento_gb', $equipo->almacenamiento_gb)); ?>" class="ui-input <?php $__errorArgs = ['almacenamiento_gb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="0" placeholder="128, 256...">
                            <?php $__errorArgs = ['almacenamiento_gb'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.15s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #7c3aed;">
                    <i class="bi bi-cpu me-2"></i>Especificaciones Técnicas
                </h6>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Dispositivo</label>
                            <select name="tipo_dispositivo" class="ui-select">
                                <option value="">Sin especificar</option>
                                <?php $__currentLoopData = ['celular','laptop','desktop','tablet','servidor','impresora','monitor','router','switch','camara','ups','otro']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($opt); ?>" <?php echo e(old('tipo_dispositivo', $equipo->tipo_dispositivo ?? '') == $opt ? 'selected' : ''); ?>><?php echo e(ucfirst($opt)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Procesador</label>
                            <input type="text" name="procesador" value="<?php echo e(old('procesador', $equipo->procesador)); ?>" class="ui-input" placeholder="Ej. M3 Pro, i7-13700K">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Memoria RAM</label>
                            <input type="text" name="memoria_ram" value="<?php echo e(old('memoria_ram', $equipo->memoria_ram)); ?>" class="ui-input" placeholder="Ej. 16GB, 32GB">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Almacenamiento</label>
                            <select name="almacenamiento_tipo" class="ui-select">
                                <option value="">Sin especificar</option>
                                <?php $__currentLoopData = ['HDD','SSD','NVMe','hybrid']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($opt); ?>" <?php echo e(old('almacenamiento_tipo', $equipo->almacenamiento_tipo ?? '') == $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Capacidad de Almacenamiento</label>
                            <input type="text" name="almacenamiento_capacidad" value="<?php echo e(old('almacenamiento_capacidad', $equipo->almacenamiento_capacidad)); ?>" class="ui-input" placeholder="Ej. 512GB, 1TB">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Sistema Operativo</label>
                            <input type="text" name="sistema_operativo" value="<?php echo e(old('sistema_operativo', $equipo->sistema_operativo)); ?>" class="ui-input" placeholder="Ej. iOS 17, Windows 11">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Puertos</label>
                            <textarea name="puertos" class="ui-input" rows="2" placeholder="Ej. USB-C, HDMI, 3.5mm audio..."><?php echo e(old('puertos', $equipo->puertos)); ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Peso (gramos)</label>
                            <input type="number" name="peso_gramos" value="<?php echo e(old('peso_gramos', $equipo->peso_gramos)); ?>" class="ui-input" min="0" step="0.01" placeholder="Ej. 187">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.2s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #059669;">
                    <i class="bi bi-cash-stack me-2"></i>Precios y Estado
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="ui-select <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Seleccionar...</option>
                                <?php $__currentLoopData = ['disponible','vendido','en_reparacion','dañado','reservado','mantenimiento']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($opt); ?>" <?php echo e(old('estado', $equipo->estado) == $opt ? 'selected' : ''); ?>><?php echo e(ucfirst(str_replace('_', ' ', $opt))); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Precio de Venta <span class="text-danger">*</span></label>
                            <div class="ui-input-group input-group-lg">
                                <span class="ui-input-group-text bg-light fw-bold">$</span>
                                <input type="number" name="precio_venta" value="<?php echo e(old('precio_venta', $equipo->precio_venta)); ?>" class="ui-input <?php $__errorArgs = ['precio_venta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" step="0.01" min="0" required placeholder="0.00">
                            </div>
                            <?php $__errorArgs = ['precio_venta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Precio de Compra</label>
                            <div class="ui-input-group input-group-lg">
                                <span class="ui-input-group-text bg-light fw-bold">$</span>
                                <input type="number" name="precio_compra" value="<?php echo e(old('precio_compra', $equipo->precio_compra)); ?>" class="ui-input" step="0.01" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Proveedor</label>
                            <select name="comprado_a_proveedor_id" class="ui-select">
                                <option value="">Sin proveedor</option>
                                <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($prov->id); ?>" <?php echo e(old('comprado_a_proveedor_id', $equipo->comprado_a_proveedor_id) == $prov->id ? 'selected' : ''); ?>><?php echo e($prov->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Fecha de Compra</label>
                            <input type="date" name="fecha_compra" value="<?php echo e(old('fecha_compra', $equipo->fecha_compra?->format('Y-m-d'))); ?>" class="ui-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Factura de Compra</label>
                            <input type="text" name="factura_compra" value="<?php echo e(old('factura_compra', $equipo->factura_compra)); ?>" class="ui-input" placeholder="Ej. F-000123">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.25s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #ca8a04;">
                    <i class="bi bi-shield-check me-2"></i>Garantía
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Tipo de Garantía</label>
                            <select name="garantia_tipo" class="ui-select">
                                <option value="">Sin garantía</option>
                                <option value="fabrica" <?php echo e(old('garantia_tipo', $equipo->garantia_tipo ?? '') == 'fabrica' ? 'selected' : ''); ?>>De Fábrica</option>
                                <option value="extendida" <?php echo e(old('garantia_tipo', $equipo->garantia_tipo ?? '') == 'extendida' ? 'selected' : ''); ?>>Extendida</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Garantía Desde</label>
                            <input type="date" name="garantia_desde" value="<?php echo e(old('garantia_desde', $equipo->garantia_desde?->format('Y-m-d'))); ?>" class="ui-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Garantía Hasta</label>
                            <input type="date" name="garantia_hasta" value="<?php echo e(old('garantia_hasta', $equipo->garantia_hasta?->format('Y-m-d'))); ?>" class="ui-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="ui-card" style="--delay:.3s;">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <h6 class="fw-bold mb-3" style="color: #dc2626;">
                    <i class="bi bi-lock me-2"></i>Bloqueos y Observaciones
                </h6>
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(220,38,38,.04);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="bloqueado_icloud" value="1" id="chk_bloqueado_icloud" role="switch" style="width:3em;height:1.5em;" <?php echo e(old('bloqueado_icloud', $equipo->bloqueado_icloud) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-semibold ms-2" for="chk_bloqueado_icloud">Bloqueado iCloud</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(220,38,38,.04);">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="bloqueado_fr" value="1" id="chk_bloqueado_fr" role="switch" style="width:3em;height:1.5em;" <?php echo e(old('bloqueado_fr', $equipo->bloqueado_fr) ? 'checked' : ''); ?>>
                                <label class="form-check-label fw-semibold ms-2" for="chk_bloqueado_fr">Bloqueado FR</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="ui-label small fw-semibold">Observaciones</label>
                            <textarea name="observaciones" class="ui-input" rows="3" placeholder="Notas adicionales sobre el equipo..."><?php echo e(old('observaciones', $equipo->observaciones)); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:#38bdf8;"></i>
            <span class="fw-semibold d-none d-sm-inline">Editando: <?php echo e($equipo->serial_imei); ?> - <?php echo e($equipo->marca); ?> <?php echo e($equipo->modelo); ?></span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('equipos.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
            <button type="submit" form="equipoForm" class="ui-btn ui-btn-solid rounded-pill">
                <i class="bi bi-cloud-arrow-up me-1"></i>Guardar Cambios
            </button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/equipos/edit.blade.php ENDPATH**/ ?>