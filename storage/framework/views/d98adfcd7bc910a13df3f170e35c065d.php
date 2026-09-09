<?php $__env->startSection('title', 'Editar Documento SGC'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.form-label-custom { font-size: .85rem; font-weight: 600; color: #64748b; margin-bottom: .25rem; }
.form-control-custom:focus, .form-select-custom:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 .2rem rgba(99,102,241,.2);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page">

    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-file-earmark-edit"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Editar <?php echo e($documento->codigo); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.documentos.show', $documento)); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Editando documento SGC: <?php echo e($documento->titulo); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.documentos.update', $documento)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-custom">Código</label>
                        <input type="text" name="codigo" class="form-control form-control-custom" value="<?php echo e(old('codigo', $documento->codigo)); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control form-control-custom" value="<?php echo e(old('titulo', $documento->titulo)); ?>" required>
                        <?php $__errorArgs = ['titulo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-custom">Descripción</label>
                        <textarea name="descripcion" class="form-control form-control-custom" rows="3"><?php echo e(old('descripcion', $documento->descripcion)); ?></textarea>
                        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Categoría</label>
                        <select name="categoria" class="form-select form-select-custom">
                            <option value="">Seleccionar...</option>
                            <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e(old('categoria', $documento->categoria) == $key ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom">Formato</label>
                        <input type="text" name="formato" class="form-control form-control-custom" value="<?php echo e(old('formato', $documento->formato)); ?>" placeholder="PDF">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-custom">Versión</label>
                        <input type="text" name="version" class="form-control form-control-custom" value="<?php echo e(old('version', $documento->version)); ?>" placeholder="1.0">
                        <?php $__errorArgs = ['version'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Emisión</label>
                        <input type="date" name="fecha_emision" class="form-control form-control-custom" value="<?php echo e(old('fecha_emision', $documento->fecha_emision?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['fecha_emision'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Revisión</label>
                        <input type="date" name="fecha_revision" class="form-control form-control-custom" value="<?php echo e(old('fecha_revision', $documento->fecha_revision?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['fecha_revision'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Vencimiento</label>
                        <input type="date" name="fecha_vencimiento" class="form-control form-control-custom" value="<?php echo e(old('fecha_vencimiento', $documento->fecha_vencimiento?->format('Y-m-d'))); ?>">
                        <?php $__errorArgs = ['fecha_vencimiento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Estado</label>
                        <select name="estado" class="form-select form-select-custom">
                            <option value="borrador" <?php echo e(old('estado', $documento->estado) == 'borrador' ? 'selected' : ''); ?>>Borrador</option>
                            <option value="revision" <?php echo e(old('estado', $documento->estado) == 'revision' ? 'selected' : ''); ?>>En Revisión</option>
                            <option value="aprobado" <?php echo e(old('estado', $documento->estado) == 'aprobado' ? 'selected' : ''); ?>>Aprobado</option>
                            <option value="vigente" <?php echo e(old('estado', $documento->estado) == 'vigente' ? 'selected' : ''); ?>>Vigente</option>
                            <option value="obsoleto" <?php echo e(old('estado', $documento->estado) == 'obsoleto' ? 'selected' : ''); ?>>Obsoleto</option>
                            <option value="archivado" <?php echo e(old('estado', $documento->estado) == 'archivado' ? 'selected' : ''); ?>>Archivado</option>
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
                    <div class="col-md-4">
                        <label class="form-label-custom">Proveedor</label>
                        <select name="proveedor_id" class="form-select form-select-custom">
                            <option value="">Sin proveedor</option>
                            <?php $__currentLoopData = $proveedores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($prov->id); ?>" <?php echo e(old('proveedor_id', $documento->proveedor_id) == $prov->id ? 'selected' : ''); ?>><?php echo e($prov->nombre); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['proveedor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label-custom">Reemplazar Archivo</label>
                        <input type="file" name="archivo" class="form-control form-control-custom" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if($documento->archivo_path): ?>
                        <div class="mt-1 small text-muted">
                            Archivo actual: <?php echo e($documento->archivo_original_name ?? 'Archivo'); ?>

                            <?php if($documento->archivo_size_bytes): ?>
                            (<?php echo e(number_format($documento->archivo_size_bytes / 1024, 1)); ?> KB)
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <label class="form-label-custom">Observaciones</label>
                        <textarea name="observaciones" class="form-control form-control-custom" rows="2"><?php echo e(old('observaciones')); ?></textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Actualizar
                    </button>
                    <a href="<?php echo e(route('sgc.documentos.show', $documento)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/documentos/edit.blade.php ENDPATH**/ ?>