<?php $__env->startSection('title', 'Nueva Obra'); ?>
<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#8b5cf6;--accent-rgb:139,92,246;--accent-hover:#7c3aed">
<div class="container-fluid px-4 py-3">

    <div class="ui-header mb-4" style="--delay:.1s">
        <div class="bubble"></div><div class="bubble"></div><div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <div>
                    <span class="badge bg-white bg-opacity-25 text-white px-3 py-1 rounded-pill mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                        <i class="bi bi-plus-circle me-1"></i>NUEVO REGISTRO
                    </span>
                    <h2 class="fw-bold mb-0 text-white">Nueva Obra</h2>
                    <p class="mb-0 opacity-75">Registra una nueva obra o pieza de la galería</p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('arte.obras.index')); ?>" class="ui-btn ui-btn-primary rounded-pill px-4">
                    <i class="bi bi-arrow-left me-1"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#8b5cf6"></div>
        <div class="ui-card-body">
            <form method="POST" action="<?php echo e(route('arte.obras.store')); ?>" enctype="multipart/form-data" id="obraForm">
                <?php echo csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="ui-label" for="titulo">Título *</label>
                        <input type="text" name="titulo" id="titulo" class="ui-input <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('titulo')); ?>" required>
                        <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="artista_id">Artista *</label>
                        <select name="artista_id" id="artista_id" class="ui-select <?php $__errorArgs = ['artista_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $artistas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($a->id); ?>" <?php echo e(old('artista_id') == $a->id ? 'selected' : ''); ?>><?php echo e($a->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['artista_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-12">
                        <label class="ui-label" for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="ui-textarea" rows="3"><?php echo e(old('descripcion')); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="coleccion_id">Colección</label>
                        <select name="coleccion_id" id="coleccion_id" class="ui-select">
                            <option value="">Sin colección</option>
                            <?php $__currentLoopData = $colecciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($c->id); ?>" <?php echo e(old('coleccion_id') == $c->id ? 'selected' : ''); ?>><?php echo e($c->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="tecnica">Técnica</label>
                        <input type="text" name="tecnica" id="tecnica" class="ui-input" value="<?php echo e(old('tecnica')); ?>" placeholder="Óleo, bronce, acrílico...">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="ano_creacion">Año de creación</label>
                        <input type="number" name="ano_creacion" id="ano_creacion" class="ui-input" value="<?php echo e(old('ano_creacion')); ?>" min="1000" max="2100">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="dimensiones">Dimensiones</label>
                        <input type="text" name="dimensiones" id="dimensiones" class="ui-input" value="<?php echo e(old('dimensiones')); ?>" placeholder="50 x 40 x 5">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="material">Material</label>
                        <input type="text" name="material" id="material" class="ui-input" value="<?php echo e(old('material')); ?>" placeholder="Lienzo, mármol, madera...">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="fecha_adquisicion">Fecha de adquisición</label>
                        <input type="date" name="fecha_adquisicion" id="fecha_adquisicion" class="ui-input" value="<?php echo e(old('fecha_adquisicion')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="precio_compra">Precio compra (RD$)</label>
                        <input type="number" name="precio_compra" id="precio_compra" class="ui-input" step="0.01" min="0" value="<?php echo e(old('precio_compra', 0)); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="precio_venta">Precio venta (RD$) *</label>
                        <input type="number" name="precio_venta" id="precio_venta" class="ui-input <?php $__errorArgs = ['precio_venta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" step="0.01" min="0" value="<?php echo e(old('precio_venta')); ?>" required>
                        <?php $__errorArgs = ['precio_venta'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label" for="estado">Estado *</label>
                        <select name="estado" id="estado" class="ui-select" required>
                            <?php $__currentLoopData = ['disponible' => 'Disponible', 'vendida' => 'Vendida', 'en_exhibicion' => 'En Exhibición', 'en_consulta' => 'En Consulta']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($k); ?>" <?php echo e(old('estado', 'disponible') == $k ? 'selected' : ''); ?>><?php echo e($v); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="ui-label" for="imagen">Imagen</label>
                        <input type="file" name="imagen" id="imagen" class="ui-input" accept="image/*">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" <?php echo e(old('activo', true) ? 'checked' : ''); ?>>
                            <label class="form-check-label small fw-semibold" for="activo">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <input type="number" name="orden" class="ui-input" placeholder="Orden" value="<?php echo e(old('orden', 0)); ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-info-circle" style="color:var(--accent,#8b5cf6)"></i>
            <span class="fw-semibold d-none d-sm-inline">Nueva Obra</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('arte.obras.index')); ?>" class="ui-btn ui-btn-ghost btn-sm">
                <i class="bi bi-x-lg me-1"></i>Cancelar
            </a>
            <button type="submit" form="obraForm" class="ui-btn ui-btn-solid rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i>Guardar Obra
            </button>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/arte/obras/create.blade.php ENDPATH**/ ?>