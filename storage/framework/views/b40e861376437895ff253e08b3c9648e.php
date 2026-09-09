<?php $__env->startSection('title', 'Nueva Categoría'); ?>

<?php $__env->startPush('styles'); ?>
<?php echo $__env->make('partials.premium-ui', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<style>
.categoria-preview {
    transition: all 0.2s;
}
.categoria-preview:hover {
    transform: scale(1.05);
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="ui-page" style="--accent:#ec4899;--accent-rgb:236,72,153;--accent-hover:#db2777;">
    <div class="ui-header mb-4" style="--delay:0s">
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="bubble"></div>
        <div class="ui-header-body">
            <div class="ui-header-left">
                <div class="ui-avatar-circle">
                    <i class="bi bi-tags"></i>
                </div>
                <div>
                    <h4 class="ui-header-title">Nueva Categoría</h4>
                    <div class="ui-header-meta">Agrega una nueva clasificación para productos</div>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('categorias.index')); ?>" class="ui-btn ui-btn-primary ui-btn-sm rounded-pill">
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

    <form id="categoriaForm" action="<?php echo e(route('categorias.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="ui-card" style="--delay:.1s">
            <div class="ui-card-accent"></div>
            <div class="card-body p-4 p-md-5">
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-info-circle me-2"></i>Información de la Categoría
                    </h6>
                    <small class="text-muted">Datos básicos de la categoría</small>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre')); ?>" required placeholder="Ej. Alimentos, Bebidas, Limpieza">
                        <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Orden</label>
                        <input type="number" name="orden" id="orden" class="ui-input" value="<?php echo e(old('orden', 0)); ?>" min="0" placeholder="0">
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-8">
                        <label class="ui-label">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="ui-textarea <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Descripción opcional"><?php echo e(old('descripcion')); ?></textarea>
                        <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-lg-4">
                        <label class="ui-label">Icono (Bootstrap Icons)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i id="iconPreview" class="bi bi-grid"></i></span>
                            <input type="text" name="icono" id="icono" class="ui-input" value="<?php echo e(old('icono', 'bi-grid')); ?>" placeholder="bi-box">
                        </div>
                        <small class="text-muted mt-1 d-block">Ej: bi-box, bi-cart, bi-star</small>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <label class="ui-label">Color</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="color" name="color_picker" id="colorPicker" class="form-control form-control-color" value="<?php echo e(old('color', '#6366f1')); ?>" style="width: 50px; height: 40px;">
                            <input type="text" name="color" id="color" class="ui-input flex-grow-1" value="<?php echo e(old('color', '#6366f1')); ?>" placeholder="#6366f1" maxlength="7">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label class="ui-label">Vista Previa</label>
                        <div id="preview" class="categoria-preview d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(99,102,241,0.1); border: 2px solid #6366f1;">
                            <div id="previewIcon" class="text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #6366f1;">
                                <i class="bi bi-grid fs-4"></i>
                            </div>
                            <div>
                                <div id="previewNombre" class="fw-bold fs-5" style="color: #6366f1;">Sin Nombre</div>
                                <div id="previewDesc" class="text-muted small">La descripción aparecerá aquí</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3 pb-3 border-bottom">
                    <h6 class="fw-bold mb-0 ui-card-title">
                        <i class="bi bi-gear me-2"></i>Estado
                    </h6>
                    <small class="text-muted">Configuración de visibilidad</small>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="activa" value="1" id="activa" <?php echo e(old('activa', true) ? 'checked' : ''); ?> role="switch" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            <label class="form-check-label fw-semibold ms-2" for="activa" style="cursor: pointer;">
                                <i class="bi bi-check-circle text-success me-1"></i>Categoría activa
                            </label>
                        </div>
                        <small class="text-muted d-block mt-1 ms-1">Si está activa, los productos podrán asignarse a esta categoría.</small>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div style="height: 80px;"></div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <a href="<?php echo e(route('categorias.index')); ?>" class="ui-btn ui-btn-ghost rounded-pill">Cancelar</a>
        <button type="submit" form="categoriaForm" class="ui-btn ui-btn-solid rounded-pill px-5">
            <i class="bi bi-check-lg me-2"></i>Guardar Categoría
        </button>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const colorPicker = document.getElementById('colorPicker');
    const colorInput = document.getElementById('color');
    const iconoInput = document.getElementById('icono');
    const ordenInput = document.getElementById('orden');

    const preview = document.getElementById('preview');
    const previewNombre = document.getElementById('previewNombre');
    const previewDesc = document.getElementById('previewDesc');
    const previewIcon = document.getElementById('previewIcon');
    const iconPreview = document.getElementById('iconPreview');

    function updatePreview() {
        const nombre = nombreInput.value || 'Sin Nombre';
        const color = colorInput.value || '#6366f1';
        const icono = iconoInput.value || 'bi-grid';
        const descripcion = descripcionInput.value || 'La descripción aparecerá aquí';

        previewNombre.textContent = nombre;
        previewNombre.style.color = color;
        previewDesc.textContent = descripcion;
        preview.style.background = color + '15';
        preview.style.borderColor = color;
        previewIcon.style.background = color;
        previewIcon.innerHTML = '<i class="' + icono + ' fs-4"></i>';
        iconPreview.className = icono;
    }

    nombreInput.addEventListener('input', updatePreview);
    descripcionInput.addEventListener('input', updatePreview);
    colorPicker.addEventListener('input', function() {
        colorInput.value = this.value;
        updatePreview();
    });
    colorInput.addEventListener('input', function() {
        colorPicker.value = this.value;
        updatePreview();
    });
    iconoInput.addEventListener('input', updatePreview);
    ordenInput.addEventListener('input', updatePreview);

    updatePreview();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/categorias/create.blade.php ENDPATH**/ ?>