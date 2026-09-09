<?php $__env->startSection('title', 'Cargar Documento Proveedor'); ?>

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
                    <i class="bi bi-building-add"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Cargar Documento - <?php echo e($proveedor->nombre); ?></h4>
                    <div class="ui-header-meta">
                        <a href="<?php echo e(route('sgc.documentos-proveedor', $proveedor)); ?>" class="text-white-50 text-decoration-none small me-2">
                            <i class="bi bi-arrow-left me-1"></i> Volver
                        </a>
                        Cargar un nuevo documento para el proveedor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.1s">
        <div class="ui-card-accent"></div>
        <div class="ui-card-body">
            <form action="<?php echo e(route('sgc.documentos-proveedor.store', $proveedor)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    <?php if($documentosSgc): ?>
                    <div class="col-md-6">
                        <label class="form-label-custom">Documento SGC asociado <span class="text-danger">*</span></label>
                        <select name="documento_sgc_id" class="form-select form-select-custom" required>
                            <option value="">Seleccionar documento SGC...</option>
                            <?php $__currentLoopData = $documentosSgc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($doc->id); ?>" <?php echo e(old('documento_sgc_id') == $doc->id ? 'selected' : ''); ?>>
                                <?php echo e($doc->codigo); ?> - <?php echo e(Str::limit($doc->titulo, 40)); ?>

                            </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['documento_sgc_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <label class="form-label-custom">Descripción del documento</label>
                        <input type="text" name="descripcionDocumento" class="form-control form-control-custom" value="<?php echo e(old('descripcionDocumento')); ?>">
                        <?php $__errorArgs = ['descripcionDocumento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label-custom">Fecha Carga <span class="text-danger">*</span></label>
                        <input type="date" name="fechaCarga" class="form-control form-control-custom" value="<?php echo e(old('fechaCarga', date('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['fechaCarga'];
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
                        <input type="date" name="fechaVencimiento" class="form-control form-control-custom" value="<?php echo e(old('fechaVencimiento')); ?>">
                        <?php $__errorArgs = ['fechaVencimiento'];
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
                            <option value="por_cargar" <?php echo e(old('estado')=='por_cargar' ? 'selected' : ''); ?>>Por Cargar</option>
                            <option value="vigente" <?php echo e(old('estado')=='vigente' ? 'selected' : ''); ?>>Vigente</option>
                            <option value="pendiente" <?php echo e(old('estado')=='pendiente' ? 'selected' : ''); ?>>Pendiente</option>
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
                    <div class="col-md-12">
                        <label class="form-label-custom">Archivo <span class="text-danger">*</span></label>
                        <input type="file" name="archivo" class="form-control form-control-custom" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                        <?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4 pt-3 border-top">
                    <button type="submit" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> Guardar
                    </button>
                    <a href="<?php echo e(route('sgc.documentos-proveedor', $proveedor)); ?>" class="ui-btn ui-btn-ghost ui-btn-sm rounded-pill px-4">
                        <i class="bi bi-x-lg me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/sgc/proveedores/documentos/create.blade.php ENDPATH**/ ?>