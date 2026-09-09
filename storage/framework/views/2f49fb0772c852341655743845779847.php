<?php $__env->startSection('title', isset($plan) ? 'Editar Plan' : 'Nuevo Plan'); ?>

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
                    <i class="bi bi-card-checklist"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1"><?php echo e(isset($plan) ? 'Editar Plan' : 'Nuevo Plan'); ?></h2>
                    <p class="mb-0 opacity-75"><?php echo e(isset($plan) ? $plan->nombre : 'Configura los l&iacute;mites y precios del plan'); ?></p>
                </div>
            </div>
            <div class="ui-header-actions">
                <a href="<?php echo e(route('owner.plans.index')); ?>" class="ui-btn ui-btn-primary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="ui-card" style="--delay:.15s">
        <div class="ui-card-accent" style="background:#3b82f6"></div>
        <div class="card-body p-4">
            <form method="POST" action="<?php echo e(isset($plan) ? route('owner.plans.update', $plan) : route('owner.plans.store')); ?>" id="planForm">
                <?php echo csrf_field(); ?>
                <?php if(isset($plan)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Informaci&oacute;n B&aacute;sica</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="ui-input <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre', $plan->nombre ?? '')); ?>" required placeholder="Ej: Profesional">
                    </div>
                    <div class="col-md-6">
                        <label class="ui-label fw-bold">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="ui-input <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('slug', $plan->slug ?? '')); ?>" required placeholder="profesional">
                    </div>
                    <div class="col-12">
                        <label class="ui-label fw-bold">Descripci&oacute;n</label>
                        <input type="text" name="descripcion" class="ui-input <?php $__errorArgs = ['descripcion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('descripcion', $plan->descripcion ?? '')); ?>" placeholder="Ideal para PYMES">
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3"><i class="bi bi-cash-coin me-2 text-success"></i>Precios</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Mensual <span class="text-danger">*</span></label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" name="precio_mensual" class="ui-input <?php $__errorArgs = ['precio_mensual'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('precio_mensual', $plan->precio_mensual ?? '')); ?>" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Implementaci&oacute;n</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" name="precio_implementacion" class="ui-input" value="<?php echo e(old('precio_implementacion', $plan->precio_implementacion ?? '')); ?>" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="ui-label fw-bold">Precio Lanzamiento (Oferta)</label>
                        <div class="ui-input-group">
                            <span class="ui-input-group-text">RD$</span>
                            <input type="number" name="precio_lanzamiento" class="ui-input" value="<?php echo e(old('precio_lanzamiento', $plan->precio_lanzamiento ?? '')); ?>" step="0.01" min="0">
                        </div>
                        <small class="text-muted d-block mt-1">Implementaci&oacute;n + primer mes desde RD$ 7,500</small>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3"><i class="bi bi-sliders me-2 text-warning"></i>L&iacute;mites del Plan</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-sm-6">
                        <label class="ui-label fw-bold">M&aacute;x. Usuarios</label>
                        <input type="number" name="max_usuarios" class="ui-input" value="<?php echo e(old('max_usuarios', $plan->max_usuarios ?? '')); ?>" min="0" placeholder="Vac&iacute;o = ilimitado">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="ui-label fw-bold">M&aacute;x. Sucursales</label>
                        <input type="number" name="max_sucursales" class="ui-input" value="<?php echo e(old('max_sucursales', $plan->max_sucursales ?? '')); ?>" min="0" placeholder="Vac&iacute;o = ilimitado">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="ui-label fw-bold">M&aacute;x. Empresas</label>
                        <input type="number" name="max_empresas" class="ui-input" value="<?php echo e(old('max_empresas', $plan->max_empresas ?? '')); ?>" min="0" placeholder="Vac&iacute;o = ilimitado">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="ui-label fw-bold">Orden</label>
                        <input type="number" name="orden" class="ui-input" value="<?php echo e(old('orden', $plan->orden ?? 0)); ?>" min="0">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6 d-flex align-items-center gap-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="activo" class="form-check-input" value="1" id="activo" <?php echo e(old('activo', $plan->activo ?? true) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="activo">Activo</label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="recomendado" class="form-check-input" value="1" id="recomendado" <?php echo e(old('recomendado', $plan->recomendado ?? false) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-bold small" for="recomendado">Recomendado</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3"><i class="bi bi-check2-circle me-2 text-info"></i>Features (incluidos en el plan)</h5>
                <div class="mb-2">
                    <button type="button" class="ui-btn ui-btn-ghost" onclick="addFeature()"><i class="bi bi-plus-lg me-1"></i>Agregar feature</button>
                </div>
                <div id="featuresContainer" class="d-flex flex-column gap-2 mb-4">
                    <?php $features = old('features', $plan->features ?? []); ?>
                    <?php $__empty_0 = true; $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_0 = false; ?>
                        <div class="ui-input-group">
                            <input type="text" name="features[]" class="ui-input" value="<?php echo e($feature); ?>">
                            <button type="button" class="ui-action ui-action-delete ms-2" onclick="this.closest('.ui-input-group').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_0): ?>
                        <div class="ui-input-group">
                            <input type="text" name="features[]" class="ui-input" placeholder="Ej: Facturaci&oacute;n + e-CF/DGII">
                            <button type="button" class="ui-action ui-action-delete ms-2" onclick="this.closest('.ui-input-group').remove()"><i class="bi bi-x-lg"></i></button>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-4">

                <h5 class="fw-bold mb-3"><i class="bi bi-grid me-2" style="color:#8b5cf6;"></i>M&oacute;dulos permitidos</h5>

                <div class="mb-3">
                    <label class="ui-label fw-bold">Auto-seleccionar m&oacute;dulos por Tipo de Negocio</label>
                    <select id="businessTypeSelector" class="ui-select" aria-label="Seleccionar tipo de negocio">
                        <option value="">&mdash; Seleccionar tipo para auto-llenar m&oacute;dulos &mdash;</option>
                        <?php $__currentLoopData = $businessTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bt->id); ?>" <?php echo e((isset($preSelectedBusinessType) && $preSelectedBusinessType == $bt->id) ? 'selected' : ''); ?>><?php echo e($bt->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <small class="text-muted d-block mt-1">Selecciona un tipo para auto-marcar sus m&oacute;dulos. Puedes ajustar despu&eacute;s manualmente.</small>
                </div>

                <small class="text-muted d-block mb-2">Si no marcas ninguno, el plan permite todos los m&oacute;dulos del tipo de negocio.</small>
                <div class="row g-2 mb-4">
                    <?php $planModulos = old('modulos', $plan->modulos ?? []); ?>
                    <?php $__currentLoopData = $modulos->groupBy('categoria'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria => $modulosCat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4">
                            <div class="rounded-3 p-3 h-100" style="background:rgba(241,245,249,.5);border:1px solid #e2e8f0;">
                                <div class="fw-bold small text-uppercase text-muted mb-2"><?php echo e(ucfirst($categoria)); ?></div>
                                <?php $__currentLoopData = $modulosCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check form-switch mb-1">
                                        <input type="checkbox" name="modulos[]" class="form-check-input" value="<?php echo e($modulo->key); ?>" id="mod_<?php echo e($modulo->key); ?>" <?php echo e(in_array($modulo->key, $planModulos) ? 'checked' : ''); ?>>
                                        <label class="form-check-label small" for="mod_<?php echo e($modulo->key); ?>"><?php echo e($modulo->label); ?></label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="ui-sticky-bar">
    <div class="ui-sticky-bar-inner">
        <span class="text-muted small me-auto"><i class="bi bi-info-circle me-1"></i><?php echo e(isset($plan) ? 'Editando plan' : 'Creando plan'); ?></span>
        <button type="submit" form="planForm" class="ui-btn ui-btn-solid">
            <i class="bi bi-save me-2"></i>Guardar Plan
        </button>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function addFeature() {
    const div = document.createElement('div');
    div.className = 'ui-input-group';
    div.innerHTML = '<input type="text" name="features[]" class="ui-input" placeholder="Ej: Reportes avanzados">' +
                    '<button type="button" class="ui-action ui-action-delete ms-2" onclick="this.closest(\'.ui-input-group\').remove()"><i class="bi bi-x-lg"></i></button>';
    document.getElementById('featuresContainer').appendChild(div);
}

document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('businessTypeSelector');
    if (!selector) return;

    selector.addEventListener('change', async function() {
        const typeId = this.value;
        const checkboxes = document.querySelectorAll('input[name="modulos[]"]');

        checkboxes.forEach(cb => cb.checked = false);

        if (!typeId) return;

        try {
            const resp = await fetch(`/business-types/${typeId}/modules-data`);
            const data = await resp.json();
            const modulos = data.modulos || [];

            checkboxes.forEach(cb => {
                if (modulos.includes(cb.value)) {
                    cb.checked = true;
                }
            });
        } catch (e) {
            console.error('Error cargando m\u00f3dulos:', e);
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/sistema-facturacion/resources/views/owner/planes/form.blade.php ENDPATH**/ ?>